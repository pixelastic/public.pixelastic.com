<?php
/**
 *	UsersController
 *
 *	TODO : Force the hashing to use sha256 instead of the default sha1
 **/
class UsersController extends AppController {
	// Components
	var $components = array(
		'CaracoleUsers.Openid' => array('use_database' => true),
		'Email'
	);

	/**
	 *	login
	 *	General method used to log the user in. The same method is used for admin_ and member_ methods
	 *	If an openid form data is passed, it will try to log the user with open id
	 **/
	function login() {
		// Setting a default page to redirect
		$defaultRedirectPage = empty($this->params['admin']) ? Configure::read('SiteUrl.default') : Configure::read('SiteUrl.admin');
		// User already loggued in and trying to access /admin/login, we redirect it to the admin panel
		if (!empty($this->params['admin']) && $this->CaracoleAuth->actionAllowed('pages', 'admin_home')) {
			return $this->redirect($defaultRedirectPage);
		}
		//Setting the default page to redirect to
		if (!$this->Session->check('Auth.requestedPage')) {
			$this->Session->write('Auth.requestedPage', $defaultRedirectPage);
		}


		// Parsing openId response
		if ($this->Openid->isResponse()) {
			$activeUser = $this->Openid->getUser();
			// No user found
			if (empty($activeUser)) {
				$this->Session->setFlash($this->Openid->error(), 'error');
				return $this->render();
			}
			// Found, logging him in
			$this->CaracoleAuth->setActiveUser($activeUser[$this->model->alias]['id']);

			// Persistent login if checked before OpenId redirect
			if ($this->Session->check('Auth.persistOpenId') && $this->Session->read('Auth.persistOpenId')) {
				$this->Session->delete('Auth.persistOpenId');
				$this->CaracoleAuth->persist($activeUser[$this->model->alias]['id']);
			}
			return $this->redirect($this->Session->read('Auth.requestedPage'));
		}

		// Displaying form if no data submitted
		if (empty($this->data)) {
			return $this->render();
		}



		// Authenticating using OpenID
		if (!empty($this->data[$this->model->alias]['openid'])) {
			// Saving in session that we should remember the user
			$this->Session->write('Auth.persistOpenId', $this->data['Options']['is_remember']);
			// Trying to authenticate
			if (!$this->Openid->authenticate($this->data[$this->model->alias]['openid'])) {
				$this->Session->setFlash($this->Openid->error(), 'error');
				return $this->render();
			}
			return true;
		}

		// Getting user id
		$activeUser = $this->model->find('first', array(
			'fields' => array($this->model->alias.'.id'),
			'conditions' => array(
				$this->model->alias.'.name' => $this->data[$this->model->alias]['name'],
				$this->model->alias.'.password' => Security::hash($this->data[$this->model->alias]['password'], null, true)
			)
		));
		// Trying to log him in
		$this->CaracoleAuth->setActiveUser($activeUser[$this->model->alias]['id']);

		// Not found, error
		if (!$this->CaracoleAuth->isLogguedIn()) {
			$this->Session->setFlash(__d('caracole_users', 'Access denied. Your login or your password is incorrect.', true), 'error');
			return $this->render();
		}

		// Persistent login
		if (!empty($this->data['Options']['is_remember'])) {
			$this->CaracoleAuth->persist($activeUser[$this->model->alias]['id']);
		}
		// Redirecting to the initially asked page
		return $this->redirect($this->Session->read('Auth.requestedPage'));
	}
	/**
	 *	admin_login
	 *	Alias to the main login method
	 **/
	function admin_login() {
		// Setting the correct layout as well as needed view variables
		$this->layout = 'admin_logout';
		$this->set('mainToolbar', $this->model->adminSettings['toolbar']['main']['login']);
		$this->login();
	}
	/**
	 *	member_login
	 *	Alias to the main login method
	 **/
	function member_login() {
		$this->login();
	}

	/**
	 *	logout
	 *	Loging out the current user and redirecting to the index
	 **/
	function logout() {
		$this->CaracoleAuth->logout();
		return $this->redirect(empty($this->params['admin']) ? Configure::read('SiteUrl.default') : Configure::read('SiteUrl.admin'));

	}
	/**
	 *	admin_logout
	 *	Alias to the main logout method
	 **/
	function admin_logout() {
		$this->logout();
	}
	/**
	 *	member_logout
	 *	Alias to the main logout method
	 **/
	function member_logout() {
		$this->logout();
	}

	/**
	 *	pass
	 *	Will give the user a chance to regenerate its password. We will ask for its email, and send a regenerate link to it.
	 *	If the token is valid, we present a "change password" form.
	 **/
	function pass($passToken = null) {
		// Step 1 : Displaying the classic form
		if (empty($passToken)) {
			// Rendering form
			if (empty($this->data)) return $this->render();

			// Form submitted, we will validate it
			$this->model->validate = array(
				'name' => array(
					'emailExists' => array(
						'rule' => array('__validateEmailExists'),
						'message' => __d('caracole_users', "Sorry, but this email is unknown.", true),
					),
					'mailValid' => array(
						'rule' => array('email', false),
						'message' => __d('caracole_users', "This email does not appear to be valid.", true)
					),
					'notEmpty' => array(
						'rule' => 'notEmpty',
						'message' => __d('caracole_users', "You have to type your email.", true)
					)
				)
			);
			$this->model->create($this->data);

			// Does not validate, we stop
			if (!$this->model->validates()) return $this->render();

			// We get the user
			$data = $this->model->find('first', array('conditions' => array($this->model->alias.'.name' => $this->data[$this->model->alias]['name'])));
			$this->model->create($data);

			// We will do some cleanup and remove all others tokens of this user, expired tokens and used tokens
			// There can only be one pass token active at one moment.

			$this->model->UserPassToken->deleteAll(array(
				'OR' => array(
					'UserPassToken.user_id' => $this->model->id,
					'UserPassToken.expires <=' => date('Y-m-d H:i:s'),
					'UserPassToken.is_used' => 1
				)
			));

			// We now create a new token
			$token = CaracoleSecurity::randomToken();
			$hashedToken = Security::hash($token, null, true);
			// We save the token in the database
			$this->model->UserPassToken->create(array(
				'user_id' => $this->model->id,
				'token' => $hashedToken,
				'is_used' => 0,
				'expires' => date('Y-m-d H:i:s', strtotime('+24 hours'))
			));
			$this->model->UserPassToken->save();

			// We send a mail to the user, with a link to change its password
			$passToken = base64_encode($this->model->id.':'.$token);
			$this->set(array(
				'item' => $this->model->data[$this->model->alias],
				'siteName' => Configure::read('Site.name'),
				'url' => Router::url(array(
					'plugin' => $this->params['plugin'],
					'controller' => $this->params['controller'],
					'action' => $this->params['action'],
					'admin' => !empty($this->params['admin']),
					'passToken' => $passToken
				), true)
			));

			$this->Email->controller = $this;
			$this->Email->to = $this->model->data[$this->model->alias]['name'];
			$this->Email->subject = sprintf(__d('caracole_users', '%1$s : Regenerating your password', true), Configure::read('Site.name'));
			$this->Email->replyTo = Configure::read('Email.pass');
			$this->Email->from = sprintf('%1$s <%2$s>', Configure::read('Site.name'), Configure::read('Email.pass'));
			$this->Email->template = 'pass';
			$this->Email->sendAs = 'both';

			// Sending mail
			//$this->Email->delivery = 'debug';
			$this->Email->send();
			//debug($this->Session->read('Message.email.message'), true);

			// Rendering a page telling them that all went well
			return $this->render($this->action.'_sent');
		}

		// Step 2 : Changing password
		$decodedToken = base64_decode($passToken);
		// Stopping if malformed token
		if (!strpos($decodedToken, ':')) return $this->cakeError('error404');
		list($userId, $token) = explode(':', $decodedToken);
		// Stopping if no user
		if (empty($userId)) return $this->cakeError('error404');

		// We delete all expired token of this user
		$this->model->UserPassToken->deleteAll(array(
			'user_id' => $userId,
			'expires <=' => date('Y-m-d H:i:s')
		));

		// We grab the current token
		$hashedToken = Security::hash($token, null, true);
		$token = $this->model->UserPassToken->find('first', array('conditions' => array('user_id' => $userId, 'token' => $hashedToken)));

		// If no token, we stop
		if (empty($token)) {
			// We remove all tokens of that user
			$this->model->UserPassToken->deleteAll(array('user_id' => $userId));
			return $this->render($this->action.'_token_error');
		}

		// If no data submitted, we display the password changer form
		if (empty($this->data)) return $this->render($this->action.'_form');

		// Updating password
		$this->model->create($this->data);

		// Does not validate, we stop
		if (!$this->model->validates()) return $this->render($this->action.'_form');

		// We save the password
		$this->model->id = $userId;
		$this->model->saveField('password', Security::hash($this->data[$this->model->alias]['password'], null, true));

		// We log the user with its new password
		$this->CaracoleAuth->setActiveUser($userId);

		// We delete the pass token
		$this->model->UserPassToken->delete($token['UserPassToken']['id']);

		// Rendering a success message
		return $this->render($this->action.'_form_ok');

	}

	/**
	 *	admin_pass
	 *	Alias to the main pass method
	 **/
	function admin_pass($passToken = null) {
		// Setting the correct layout as well as needed view variables
		$this->layout = 'admin_logout';
		$this->pass($passToken);
	}
	/**
	 *	member_pass
	 *	Alias to the main pass method
	 **/
	function member_pass($passToken = null) {
		$this->pass($passToken);
	}


	/**
	 *	admin_edit
	 *	Editing a user. We have to hide the password (because it is encrypted anyway) and change if a new value is set
	 **/
	function admin_edit($id = null) {
		// When editing a user, we need to change the help tooltips
		if (!empty($id)) {
			$this->model->adminSettings['fields']['password']['help'] = __d('caracole_users', "Keep this field empty if you don't want to change the current pass.", true);
			$this->model->adminSettings['fields']['password_confirm']['help'] = __d('caracole_users', "Keep this field empty if you don't want to change the current pass (only if you wish to change your password).", true);
		}

		//	Submitting data
		if (!empty($this->data)) {
			// If a password is set, we encrypt it
			if (!empty($this->data[$this->model->alias]['password'])) {
				$this->data[$this->model->alias]['password'] = Security::hash($this->data[$this->model->alias]['password'], null, true);
				$this->data[$this->model->alias]['password_confirm'] = Security::hash($this->data[$this->model->alias]['password_confirm'], null, true);
			} else {
				// If the password is empty in edit mode, it means we won't change it
				if (!empty($id)) {
					unset($this->data[$this->model->alias]['password']);
					unset($this->model->validate['password_confirm']);
				}
			}
		}

		//	Initial actions
		parent::admin_edit($id);

		// We clear the password fields
		$this->data[$this->model->alias]['password'] = '';
		$this->data[$this->model->alias]['password_confirm'] = '';
	}






}
