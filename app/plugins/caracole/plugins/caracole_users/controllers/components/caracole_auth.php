<?php
/**
 *	CaracoleAuth
 *	Component used to authenticate users and allow/deny access to methods based on their rights.
 *
 *	It will use the CaracoleUser model as a default, but you can override it by changing the Configure(User.authModel) key
 *
 *	In a nutshell, here are the internal mechanics :
 *		- When the user first logs in, we check the database against its supplied username and a hashed version of the
 *		supplied password.
 *		- If it does not match, nothing happens, authentication is refused.
 *		- If it match, we consider the user as loggued id and we save its full profile in Session.
 *		- On subsequent requests, we check the session, if there is a user here, we re-fetch an up to date profile based
 *		on the id saved in session. And we resave it to session.
 *		- This would keep the user loggued in until its session expires.
 *
 *		Now for the "Remember me" feature :
 *		- If the user checked the "Remember me" checkbox, we will generate a random token
 *		- We will create a cookie with the user_id and this token
 *		- We will also create a new UserLoginToken with this user_id and a hashed version of the token.
 *		- The next time the user will hit the website without a session but with a cookie, we will do a check against the user_id
 *		and the hashed version of the token
 *		- If there is a match, then we log the user id. It means that this user was previously able to login, and just used its "remember me" feature.
 *		- We will also regenerate a new token and update the cookie and database with it. This way each token is only usable once.
 *		- If the user_id/token did not match, it most surely was an attempt at cookie forgery, so we'll delete all the UserLoginToken of this user, just to be sure.
 *
 *		Using this method, we will allow the "Remember me" feature to work with any type of authenication, not only id/pass check.
 *		There is still a little windows of insecureness : if someone steals the cookie of a legitimate user while he is still loggued in using his session,
 *		Then the attacker will be loggued in until its session expires.
 **/
class CaracoleAuthComponent extends Object {
	// Default acl rights
	var $accessRights = array(
		'default' => '*:*,!*:admin_*,!*:member_*',
		'is_member' => '*:*,!*:admin_*',
		'is_admin' => '*:*',
	);
	// Action white list that should not be tested
	var $whiteList = array('admin_login', 'admin_logout', 'admin_pass', 'member_login', 'member_logout', 'member_pass');

	/**
	 *	initialize
	 *	Fired before the controller beforeFilter. Used to init the component
	 **/
	function initialize(&$controller) {
		$this->controller = &$controller;

		// We won't apply the component for the main install controller nor
		if ($controller->name=='Installs') return true;

		// Saving model name and plugin
		$useModel = Configure::read('Auth.useModel');
		if (strpos($useModel, '.')) {
			list($this->modelPlugin, $this->modelName) = explode('.', $useModel);
			$this->modelPlugin = Inflector::underscore($this->modelPlugin);
		} else {
			$this->modelPlugin = false;
			$this->modelName = $useModel;
		}

		// We load the User model in this component.
		$this->User = &ClassRegistry::init($useModel);
		// Manually initiating behaviors
		if (!empty($this->User->Behaviors)) {
			foreach($this->User->Behaviors->_attached as $behavior) {
				$this->User->Behaviors->{$behavior}->setup($this->User);
			}
		}

		// Login and logout urls
		$url = array('controller' => Inflector::tableize($this->modelName), 'plugin' => $this->modelPlugin);
		$this->urlLogin = array_merge($url, array('action' => 'login'));
		$this->urlLogout = array_merge($url, array('action' => 'logout'));
	}

	/**
	 *	startup
	 *	Fired after the controller beforeFilter. Will check if the current activeUser can access the action
	 **/
	function startup(&$controller) {
		// We won't apply the component for the main install controller
		if ($this->controller->name=='Installs') return true;

		// Saving the active user
		$this->setActiveUser($this->getActiveUser());

		// Ok to continue
		if ($this->actionAllowed()) return true;

		// Saving in session the original requested page and error message
		$this->controller->Session->write('Auth.requestedPage', $this->controller->here);
		$this->controller->Session->setFlash(__d('caracole_users', 'You are not allowed to access this page.', true), 'error');

		// Redirect to login page
		$this->controller->redirect($this->urlLogin, 401);

	}

	/**
	 *	actionAllowed
	 *	Check if a user can access a given action
	 *
	 *	@param	string	$controllerName		Controller name (default to current controller)
	 *	@param	string	$actionName			Action name (default to current action)
	 *	@param	array	$user				User data set to test (default to current active user)
	 **/
	function actionAllowed($controllerName = null, $actionName = null, $user = null) {
		// Defaults
		if (empty($controllerName)) $controllerName = $this->controller->name;
		if (empty($actionName)) $actionName = $this->controller->action;
		if (empty($user)) $user = $this->getActiveUser();

		// Checking whitelist
		if (in_array($actionName, $this->whiteList)) return true;

		// Default user values
		$user = Set::merge(array(
			'User' => array(
				'acl' => '',
				'is_admin' => false,
				'is_member' => false
			)
		), $user);

		// User acl string
		$acl = $user['User']['acl'];
		if (!empty($user['User']['is_admin'])) $acl = $this->accessRights['is_admin'].','.$acl;
		elseif (!empty($user['User']['is_member'])) $acl = $this->accessRights['is_member'].','.$acl;
		else $acl = $this->accessRights['default'].','.$acl;

		// Testing each ACL rule, from left to right, each one overriding the previous one
		$allowed = false;
		$rules = explode(',', $acl);
		foreach($rules as $rule) {
			if (empty($rule)) continue;
			// Negate rule
			if(substr($rule,0,1)=='!') {
				$result = false;
				$rule = substr($rule,1);
			} else {
				$result = true;
			}

			// Transform in regexp
			$rule = str_replace('*','.*', $rule);
			list($testControllerName, $testActionName) = explode(':', $rule);
			// Check that it match and return the defined result (true or false depending on the negate rule)
			if (preg_match('/^'.$testControllerName.'$/i', $controllerName) && preg_match('/^'.$testActionName.'$/i', $actionName)) {
				$allowed = $result;
			}
		}
		return $allowed;
	}

	/**
	 *	logout
	 *	Will clear all cached and saved data about the current active user
	 **/
	function logout() {
		$this->controller->Session->delete('Auth.activeUser');
		if ($this->controller->Cookie->read('Auth')) $this->controller->Cookie->delete('Auth.activeUser');
		$defaultUser = $this->__getDefaultUser();
		Configure::write('Auth.activeUser', $defaultUser);
		$this->activeUser = $defaultUser;
		$this->controller->set('activeUser', $defaultUser);
	}

	/**
	 *	isLogguedIn
	 *	Returns true if the active user is loggued in
	 **/
	function isLogguedIn() {
		$activeUser = $this->getActiveUser();
		return !empty($activeUser['is_loggued']);
	}



	/**
	 *	getActiveUser
	 *	Gets the active user. Will look in the following places, in that order :
	 *		- In the component cache ($this->activeUser)
	 *		- In session
	 *		- In cookie
	 *		- Reverts to default user if none is found
	 *
	 *	@param	boolean		$forceReset		If set to true, will skip the cache and re-read session and cookie
	 **/
	function getActiveUser($forceReset = false) {
		// In cache
		if (empty($forceReset) && !empty($this->activeUser)) {
			return $this->activeUser;
		}

		// Getting the user from session
		$activeUser = $this->__getUserFromSession();
		if (!empty($activeUser)) return $activeUser;

		// Getting the user from cookie
		$activeUser = $this->__getUserFromCookie();
		if (!empty($activeUser)) return $activeUser;

		// Getting the default user
		return $this->__getDefaultUser();
	}

	/**
	 *	setActiveUser
	 *	Set the specified user as the active user.
	 **/
	function setActiveUser($user = null) {
		// Default user if no correct user is given
		if (empty($user)) return $this->setActiveUser($this->__getDefaultUser());
		// Converting id to user array
		if (is_numeric($user)) {
			$user = $this->__getUserFromDatabase($user);
		}

		// Saving in the component
		$this->activeUser = $user;
		// Saving the current user app-wide
		Configure::write('Auth.activeUser', $this->activeUser);
		// Session
		$this->controller->Session->write('Auth.activeUser', $this->activeUser['User']['id']);
		// View
		$this->controller->set('activeUser', $this->activeUser);
	}


	/**
	 *	persist
	 *	Save the cookie and UserLoginToken so that the current user can use the persistent login feature
	 **/
	function persist($userId, $duration = '+2 weeks') {
		// Generating a token
		$token = CaracoleSecurity::randomToken();
		$hashedToken = Security::hash($token, null, true);
		// We save in the cookie the user_id as well as the token
		$this->controller->Cookie->write('Auth.activeUser', $userId.':'.$token, true, $duration);
		// We also create a new entry for this token
		$this->User->UserLoginToken->create(array(
			'user_id' => $userId,
			'token' => $hashedToken,
			'expires' => date('Y-m-d H:i:s', strtotime($duration))
		));
		$this->User->UserLoginToken->save();
	}


	/**
	 *	__getDefaultUser
	 *	Returns a default user
	 **/
	function __getDefaultUser() {
		return array(
			'User' => array(
				'id' => 0,
				'name' => null,
				'password' => null,
				'first_name' => __d('caracole_users', 'John', true),
				'surname' => __d('caracole_users', 'Doe', true),
				'nickname' => __d('caracole_users', 'John Doe', true),
				'acl' => '',
				'is_disabled' => 0,
				'is_member' => 0,
				'is_admin' => 0,
				'is_master' => 0
			),
			'is_loggued' => false
		);
	}

	/**
	 *	__getUserFromCookie
	 *	Reading cookies to find id and token and auto-login user.
	 *	We search a UserLoginToken for this user and this hashed token. If we got no answer, it surely is a cookie forgery.
	 *	If we got an answer, we check the validity date. If still valid, we log the user and create a new UserLoginToken.
	 *	Otherwise, we delete the UserLoginToken.
	 **/
	function __getUserFromCookie() {
		$userInfo = $this->controller->Cookie->read('Auth.activeUser');
		if (empty($userInfo)) return false;

		// Getting user_id and token
		if (!strpos($userInfo, ':')) return false;
		list($userId, $token) = explode(':', $userInfo);
		if (empty($token)) return false;
		$hashedToken = Security::hash($token, null, true);

		// We find a matching UserLoginToken
		$UserLoginToken = $this->User->UserLoginToken->find('first', array(
			'conditions' => array(
				'UserLoginToken.user_id' => $userId,
				'UserLoginToken.token' => $hashedToken
			),
			'contain' => false
		));


		// No answer ? Then the id/hash is invalid, it must be a cookie forgery attempt.
		if (empty($UserLoginToken)) {
			// We delete ALL UserLoginToken of this user to prevent any intrusion
			// TODO : We should send a warning to the user telling him that his account may have been compromised
			$this->User->UserLoginToken->deleteAll(array('UserLoginToken.user_id' => $userId));
			$this->controller->Cookie->delete('Auth.activeUser');
			return false;
		}

		// We now check that the UserLoginToken is still valid
		if ($UserLoginToken['UserLoginToken']['expires'] <= date('Y-m-d H:i:s')) {
			// We delete all expired tokens of this user
			$this->User->UserLoginToken->deleteAll(array(
				'UserLoginToken.user_id' => $userId,
				'UserLoginToken.expires <=' => date('Y-m-d H:i:s')
			));
			// We clear the cookie
			$this->controller->Cookie->delete('Auth.activeUser');
			return false;
		}

		// We delete the current token, now used
		$this->User->UserLoginToken->delete($UserLoginToken['UserLoginToken']['id']);
		// We restart the whole "Remember me" process, creating a new token
		$this->persist($userId);

		// TODO : User authenticated using this method should'nt be able to perform any sensible action
		// (like changing password). We should flag it

		return $this->__getUserFromDatabase($userId);
	}

	/**
	 *	__getUserFromDatabase
	 *	Finds the selected user in the database.
	 **/
	function __getUserFromDatabase($userId = null) {
		if (empty($userId)) return false;
		// Conditions
		$options = Set::merge(
			array(
				'conditions' => array($this->modelName.'.id' => $userId, $this->modelName.'.is_disabled' => 0),
				'contain' => array()
			),
			Configure::read('Auth.selectOptions')
		);

		// Id
		$activeUser = $this->User->find('first',$options);

		// Setting 'User' as the default key to save
		if (empty($activeUser['User']) && !empty($activeUser[$this->modelName])) {
			$activeUser['User'] = $activeUser[$this->modelName];
			unset($activeUser[$this->modelName]);
		}

		return empty($activeUser) ? array('User' => array('id' => false), 'is_loggued' => false) : array_merge($activeUser, array('is_loggued' => true));
	}

	/**
	 *	__getUserFromSession
	 *	Reading session to find name and password and finding a match in the DB
	 **/
	function __getUserFromSession() {
		if (!$this->controller->Session->check('Auth.activeUser')) return false;
		return $this->__getUserFromDatabase($this->controller->Session->read('Auth.activeUser'));
	}






}
