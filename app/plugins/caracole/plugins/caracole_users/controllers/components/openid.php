<?php
/**
 *	An openId component.
 *	Mostly based on the work of Daniel Hostetter, but rewritten to fit in the Caracole login process
 *
 *	I've updated the Yadis ParanoidHTTPFetcher to force it to use the ca-bundle.crt file on Windows
 *	Also note that Google needs an https request so be sure to have openssl enabled
 *
 *	Google does not use the SReg protocol, but the AX one, that's why we request both of them.
 *	And google will only return the email field, and only if forced to be required not if_available
 *
 **/
class OpenidComponent extends Object {
	// Will hold the message of the latest error thrown
	var $error = false;
	// Default settings
	var $defaults = array(
		'enableGoogleApps' => true,		// Should we enable Google Apps
		'store' => 'database',				// The store to use to store nonce. file or database
		'databaseConfig' => 'default'		// The database credentials to use if using database store
	);
	var $settings = array();
	var $consumer = false;
	var $store = false;

	/**
	 *	initialize
	 *	Called before the beforeFiler. Will load all the required files
	 **/
	function initialize(&$controller, $settings = array()) {
		// Reference to controller
		$this->controller = $controller;
		// Loading settings
		$this->settings = Set::merge($this->defaults, $settings);

		// We add new include paths
		$vendorPath = App::pluginPath('CaracoleUsers').'vendors'.DS;
		$path = ini_get('include_path');
		ini_set('include_path', $vendorPath.PATH_SEPARATOR.$vendorPath.'pear'.DS.PATH_SEPARATOR.$path);

		// Setting the randomness to null on windows
		if (Folder::isWindowsPath(__FILE__) && !defined('Auth_OpenID_RAND_SOURCE')) {
			define('Auth_OpenID_RAND_SOURCE', null);
		}
	}



	/**
	 *	authenticate
	 *	Tries to authenticate the user using the defined provider.
	 *	@param	string 	$url		The provider url
	 *	@param	array	$options	An array of options to pass.
	 *								- return : The url where the authentication should return to. Default to current
	 *								- realm	: The realm of the authentication. Default to current server
	 *								- data : Array of data to pass to the authentication process
	 *										- sreg_required : Required fields for the SReg
	 *										- sreg_optional : Optional fields for the SReg
	 *										- ax : Fields for the AX, must be split in two keys : required and if_available
	 **/
	function authenticate($url, $options = array()) {
		// Default options
		$options = Set::merge(array(
			'return' => Router::url('', true),
			'realm' => FULL_BASE_URL.'/',
			'data' => array(
				'sreg_required' => array(),
				'sreg_optional' => array('email', 'nickname', 'fullname', 'gender'),
				'ax' => array(
					'required' => array(
						'http://axschema.org/contact/email',
					),
					'if_available' => array(
						'http://axschema.org/namePerson/friendly',
						'http://axschema.org/namePerson',
						'http://axschema.org/person/gender'
					),

				)
			)
		), $options);

		// Getting the request
		$request = $this->__getRequest($url);

		// Invalid openId
		if (!isset($request) || !$request) {
		    return $this->error(__d('caracole_users', 'Invalid OpenId', true));
		}

		// Adding SReg information
		$sregRequest = Auth_OpenID_SRegRequest::build($options['data']['sreg_required'], $options['data']['sreg_optional']);
		if ($sregRequest) {
			$request->addExtension($sregRequest);
		}

		// Adding AX information
		$axRequest = new Auth_OpenID_AX_FetchRequest();
		foreach($options['data']['ax'] as $axType => &$axFields) {
			foreach($axFields as $ax) {
				$axRequest->add(Auth_OpenID_AX_AttrInfo::make($ax, 1, ($axType=='required')));
			}
		}
		$request->addExtension($axRequest);

		// Redirect to auth page
		$redirectUrl = $request->redirectUrl($options['realm'], $options['return']);
		if ($this->isOpenIdFailure($redirectUrl)) {
			return $this->error(sprintf(__d('caracole_users', 'Could not redirect to server : %1$s', true), $redirectUrl->message));
		}

		// We should use a classic redirect whenever possible, but for data > 2kb, we need to use a dummy post form
		if ($request->shouldSendRedirect()) {
			$this->controller->redirect($redirectUrl);
		} else {
			if (!$this->displayFormRedirect($request, $options)) {
				return $this->error();
			}
		}
		return true;
	}

	/**
	 *	__getRequest
	 *	Return the openid request object
	 **/
	function __getRequest($url) {
		return $this->__getConsumer()->begin($url);
	}

	/**
	 *	isResponse
	 *	Return true if the current page is a valid OpenId response (needs an openid_ns value in url)
	 **/
	function isResponse() {
		return (count($_GET) > 1 && isset($this->controller->params['url']['openid_ns']));
	}

	/**
	 *	getUser
	 *	Gets the user corresponding to the current response
	 *	@param	array	$options	Array of options to pass
	 *								- url : Url to check the response for. Default to current url
	 *								- fallbackToMail : If no user is found using this openId, we fallback to user with defined email. Default to true
	 *								- create : Autocreate a new user if the openid is unknown.
	 **/
	function getUser($options = array()) {
		// Options
		$options = Set::merge(array(
			'url' => Router::url('', true),
			'fallbackToMail' => true,
			'create' => true
		), $options);

		// Getting response
		$response = $this->__getResponse($options);


		// Response errors
		if ($response->status==Auth_OpenID_CANCEL) {
			return $this->error(__d('caracole_users', 'OpenId verification cancelled', true));
		}
		if ($response->status == Auth_OpenID_FAILURE) {
			return $this->error(sprintf(__d('caracole_users', 'OpenId verification failed : %1$s', true), $response->message));
		}

		// Finding matching user
		$userAlias = $this->controller->model->alias;
		$user = $this->controller->model->find('first', array('conditions' => array($userAlias.'.openid' => $response->identity_url)));
		if (!empty($user)) {
			return $user;
		}

		// We create a dummy user with the return informations
		$dummyUser = $this->__parseUserFromResponse($response);

		// TODO : If a malicious user create a fake account with someone else email, when the legit user will log in
		// TODO : he will be binded to the fake account. We should instead not bind accounts but only create new one if the email address is not taken

		/*
		 // We bind the user to an existing one if a corresponding email is found
		if (!empty($options['fallbackToMail']) && !empty($dummyUser['name'])) {
			$userWithSameEmail = $this->controller->model->find('first', array(
				'conditions' => array(
					$userAlias.'.name' => $dummyUser['name'],
					$userAlias.'.openid' => ''
				)
			));
			// Updating user and returning it
			if (!empty($userWithSameEmail)) {
				$this->controller->model->create($userWithSameEmail);
				$this->controller->model->saveField('openid', $dummyUser['openid']);
				return $this->controller->model->read();
			}
		}
		*/
		// Creating a new user
		if (!empty($options['create'])) {
			if (empty($dummyUser['name'])) {
				return $this->error(sprintf(__d('caracole_users', 'You have no account registered with this openId. Please fill at least a valid email on your openId so we can create an account for you.', true), $response->message));
			}
			$this->controller->model->create($dummyUser);
			$this->controller->model->save();
			return $this->controller->model->read();
		}
		return $this->error(sprintf(__d('caracole_users', 'No user match this OpenId.', true), $response->message));

	}

	/**
	 *	__getResponse
	 *	Returns the Openid response of the current request.
	 *	We put it in its own method to ease the testing process
	 */
	function __getResponse($options) {
		return $this->__getConsumer()->complete($options['url'], $this->__getQuery());
	}




	/**
	 *	error
	 *	Get or set the latest error message
	 **/
	function error($error = null) {
		if (!isset($error)) {
			return $this->error;
		}
		$this->error = $error;
		return false;
	}

	/**
	 *	displayFormRedirect
	 *	Unfortunatly, we can't always use a classic redirect because our query will be > 2Ko, so we must cheat by using
	 *	an intermediate page with a form with auto-submit
	 **/
	function displayFormRedirect($request, $options = array()) {
		$formId = 'openid_message';
		$formHtml = $request->formMarkup($options['realm'], $options['return'], false, array('id' => $formId));

		// Error while generating form
		if ($this->isOpenIdFailure($formHtml)) {
			return $this->error(sprintf(__d('caracole_users', 'Could not redirect to server: %1$s', true), $formHtml->message));
		}

		// Displaying the redirect view
		$this->controller->set(array(
			'title_for_layout' => __d('caracole_users', 'OpenId Authentication Redirect', true),
			'formId' => $formId,
			'formHtml' => $formHtml
		));
		$this->controller->render('openid_redirect', false, ROOT.DS.APP_DIR.DS.'plugins'.DS.'caracole'.DS.'plugins'.DS.'caracole_users'.DS.'views'.DS.'users'.DS.'openid_redirect.ctp');
	}

	/**
	 *	isOpenIdFailure
	 *	Checks if the passed var is a Auth_OpenID_FailureResponse
	 **/
	function isOpenIdFailure($thing) {
		return Auth_OpenID::isFailure($thing);
	}


	/**
	 *	__getConsumer
	 *	Returns a consumer instance. Will load needed classes if Google Apps is enabled
	 **/
	function __getConsumer() {
		// Returning consumer from cache
		if (!empty($this->consumer)) {
			return $this->consumer;
		}

		App::import('Vendor', 'CaracoleUsers.consumer', array('file' => 'Auth/OpenID/Consumer.php'));
		App::import('Vendor', 'CaracoleUsers.sreg', array('file' => 'Auth/OpenID/SReg.php'));
		App::import('Vendor', 'CaracoleUsers.ax', array('file' => 'Auth/OpenID/AX.php'));
		$this->consumer = new Auth_OpenID_Consumer($this->__getStore());

		// Enabling Google Apps
		if (!empty($this->settings['enableGoogleApps'])) {
			App::import('Vendor', 'CaracoleUsers.google', array('file' => 'Auth/OpenID/google_discovery.php'));
			new GApps_OpenID_Discovery($this->consumer);
		}

		return $this->consumer;
	}

	/**
	 *	getStore
	 *	Returns a store instance based on the store setting value.
	 *	Possible values are database or file.
	 **/
	function __getStore() {
		// Getting store from cache
		if (!empty($this->store)) {
			return $this->store;
		}
		return $this->store = ($this->settings['store']=='database') ? $this->__getDatabaseStore() : $this->__getFileStore();
	}

	/**
	 *	__getDatabaseStore
	 *	Init the database store and returns an instance
	 **/
	function __getDatabaseStore() {
		App::import('Vendor', 'CaracoleUsers.peardb', array('file' => 'pear/DB.php'));
		App::import('Vendor', 'CaracoleUsers.mysqlstore', array('file' => 'Auth/OpenID/MySQLStore.php'));
		$dataSource = ConnectionManager::getDataSource($this->settings['databaseConfig']);
		//debug($dataSource);

		$dsn = array(
	    	'phptype'  => 'mysql',
	    	'username' => $dataSource->config['login'],
	    	'password' => $dataSource->config['password'],
	    	'hostspec' => $dataSource->config['host'],
	    	'database' => $dataSource->config['database'],
			'port'     => $dataSource->config['port']
		);

		$db = DB::connect($dsn);
		if (PEAR::isError($db)) {
		    return $this->error($db->getMessage());
		}

		return new Auth_OpenID_MySQLStore($db);
	}

	/**
	 *	__getFileStore
	 *	Init the file store and returns an instance
	 **/
	function __getFileStore() {
		$storePath = TMP.'openid';
		// Check writing rights
		if (!file_exists($storePath) && !mkdir($storePath)) {
			return $this->error(sprintf(
				__d('caracole_users', 'Could not create the FileStore directory %1$s.', true),
				$storePath
			));
		}

		App::import('Vendor', 'CaracoleUsers.filestore', array('file' => 'Auth/OpenID/FileStore.php'));
		return new Auth_OpenID_FileStore($storePath);
	}

	/**
	 *	__getQuery
	 *	Gets the current query, but will strip the url param (added by cake) from it
	 **/
	function __getQuery() {
		$query = Auth_OpenID::getQuery();
    	unset($query['url']);
    	return $query;
	}

	/**
	 *	__parseUserFromResponse
	 *	Will return a dummy user data array with as much fields filled as we can.
	 *	Will first grab informations from the AX response, and then from the SReg
	 **/
	function __parseUserFromResponse(&$response) {
		// Getting responses from SReg and AX
		$sreg = $this->__parseSRegResponse($this->__getSRegResponse($response));
		$ax = $this->__parseAXResponse($this->__getAXResponse($response));
		return array_merge(
			array('openid' => $response->identity_url),
			$sreg,
			$ax
		);
	}

	/**
	 *	__getSRegResponse
	 *	Returns the SReg array response from an Openid response
	 **/
	function __getSRegResponse(&$response) {
		return Auth_OpenID_SRegResponse::fromSuccessResponse($response)->contents();
	}



	/**
	 *	__parseSRegResponse
	 *	Returns a user data array filled with data coming from the SReg chunk of the response
	 **/
	function __parseSRegResponse($sreg) {
		$user = array();

		// Gender
		if (!empty($sreg['gender'])) {
			$sreg['gender'] = strtoupper($sreg['gender']);
			$map = array('M' => __d('caracole_users', 'Mr', true), 'F' => __d('caracole_users', 'Mrs', true));
			$user['gender'] = array_key_exists($sreg['gender'], $map) ? $map[$sreg['gender']] : null;
		}

		// Nickname
		if (!empty($sreg['nickname'])) {
			$user['nickname'] = $sreg['nickname'];
		} elseif (!empty($sreg['fullname'])) {
			$user['nickname'] = $sreg['fullname'];
		}

		// Email
		if (!empty($sreg['email'])) {
			$user['name'] = $sreg['email'];
		}

		return $user;
	}

	/**
	 *	__getAXResponse
	 *	Returns the AX array response from an Openid response
	 **/
	function __getAXResponse(&$response) {
		return Auth_OpenID_AX_FetchResponse::fromSuccessResponse($response)->data;
	}

	/**
	 *	__parseAXResponse
	 *	Returns a user data array filled with data coming from the AX chunk of the response
	 **/
	function __parseAXResponse($ax) {
		// Getting response
		$user = array();
		$map = array(
			'http://axschema.org/namePerson/friendly' => 'nickname',
			'http://axschema.org/contact/email' => 'name',
			'http://axschema.org/namePerson' => 'fullname',
			'http://axschema.org/person/gender' => 'gender'
		);

		foreach($ax as $axName => &$axValues) {
			// Skipping unmapped keys and empty values
			if (!array_key_exists($axName, $map) || empty($axValues)) continue;
			$user[$map[$axName]] = $axValues[0];
		}

		return $user;
	}












}
