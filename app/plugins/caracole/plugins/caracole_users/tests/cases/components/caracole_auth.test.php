5<?php
// Using default user
Configure::write('Auth.useModel', 'CaracoleUsers.User');
Configure::write('Auth.modelAlias', 'User');
Configure::write('Auth.selectOptions', array());
App::import('Component', 'CaracoleUsers.CaracoleAuth');

// CaracoleAuthTestController
 class CaracoleAuthTestController extends Controller {
    var $components = array('Cookie', 'Session', 'RequestHandler');
    var $autoRender = false;
    function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }
    function render($action = null, $layout = null, $file = null) {
        $this->renderedAction = $action;
    }
     function _stop($status = 0) {
        $this->stopped = $status;
    }

}
// Fake model
class CaracoleAuthTest {
   var $useTable = false;
}


class CaracoleAuthTestCase extends CakeTestCase {
    var $fixtures = array(
        'plugin.caracole_users.user',
        'plugin.caracole_users.user_login_token',
        'plugin.caracole_users.user_pass_token'
    );


    function startTest() {
        // Controller
        $this->controller = new CaracoleAuthTestController();
		$this->controller->constructClasses();
		$this->controller->Component->initialize($this->controller);
		$this->controller->beforeFilter();
		$this->controller->Component->startup($this->controller);

        // Component
        $this->CaracoleAuth = new CaracoleAuthComponent();

        // Mock Component
        Mock::generate('CaracoleAuthComponent');
        $this->mockCaracoleAuth = new MockCaracoleAuthComponent();
        Mock::generate('RequestHandlerComponent');
        $this->mockRequestHandler = new MockRequestHandlerComponent();

        // Starting a new cookie
        $this->controller->Cookie->destroy();
        if ($this->controller->Cookie->read('Auth.activeUser')) $this->controller->Cookie->delete('Auth.activeUser');
        // Starting a new session
        $this->controller->Session->delete('Auth');
        // Clearing configure value
        Configure::write('Auth.activeUser', null);
        Configure::write('Auth.useModel', 'CaracoleUsers.User');
        Configure::write('Auth.modelAlias', 'User');
        Configure::write('Auth.selectOptions', array());
	}

    // cakePHP Cookie component do not check if the first part of a dotted cookie value exists before deleting it
    function testLogoutWontThrowErrorIfCookieValueNotDefined() {
        $this->CaracoleAuth->initialize($this->controller);

        // To catch the undefined index error we need to overwrite the error handler function
		function errorHandlerCatchUndefinedIndexAuth($errno, $errstr, $errfile, $errline ) {
			// we are only interested in one error
			if ($errstr=='Undefined index: Auth') {
				// we will throw an exception that will ba catched in our method
				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
				return true;
			}
			// return false will keep PHP doing its default error handling
			return false;
        }
        set_error_handler("errorHandlerCatchUndefinedIndexAuth");

		// We will try to catch an ErrorException that will be thrown only if our error gets triggered
		try {
            $this->CaracoleAuth->logout();
        } catch (ErrorException $e) {
			// We MUST restore the error handler or it will mess all the other tests
			restore_error_handler();
			// We manually fail this test
			$this->fail();
			// And return to stop right here
			return;
        }

		// We MUST restore the error handler or it will mess all the other tests
		restore_error_handler();
		//And manually pass the test
		$this->pass();
    }

    // Testing if an action is allowed should always set a default acl for the user
    function testDefaultACLShouldAlwaysBeDefinedWhenTestingIfActionAllowed() {
        $this->CaracoleAuth->initialize($this->controller);

        // To catch the undefined index error we need to overwrite the error handler function
		function errorHandlerCatchUndefinedIndexAcl($errno, $errstr, $errfile, $errline ) {
			if ($errstr=='Undefined index: acl') {
				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
				return true;
			}
			return false;
        }
        set_error_handler("errorHandlerCatchUndefinedIndexAcl");

		// We will try to catch an ErrorException that will be thrown only if our error gets triggered
		try {
            $user = array('User' => array('id' => 1));
            $this->CaracoleAuth->actionAllowed('controller', 'action', $user);
        } catch (ErrorException $e) {
			restore_error_handler();
			$this->fail();
			return;
        }

		restore_error_handler();
		$this->pass();
    }

    // Initialize should stop and return true when applied to Install controller
    function testInitializeStopsWhenInstalling() {
        $this->controller->name = 'Installs';
        $result = $this->CaracoleAuth->initialize($this->controller);
        $this->assertTrue($result);
    }

    // Plugin is null and modelName is set to the current model for default models
    function testPluginIsEmptyAndModelNameIsSet() {
        Configure::write('Auth.useModel', 'CaracoleAuthTest');
        $result = $this->CaracoleAuth->initialize($this->controller);
        $this->assertFalse($this->CaracoleAuth->modelPlugin);
        $this->assertEqual($this->CaracoleAuth->modelName, 'CaracoleAuthTest');
    }

    // Plugin and model correctly set when using a custom plugin model
    function testPluginAndModelAreSetWhenUsingCustomPLuginModel() {
        Configure::write('Auth.useModel', 'CaracoleUsers.User');
        $result = $this->CaracoleAuth->initialize($this->controller);
        $this->assertEqual($this->CaracoleAuth->modelPlugin, 'caracole_users');
        $this->assertEqual($this->CaracoleAuth->modelName, 'User');
    }

    // Loading the User model inside the component
    function testLoadTheSpecifiedUserModel() {
        App::import('CaracoleUsers.User');
        Mock::generate('User');
        $mockUser = new MockUser();
        $mockUser->name = 'MockUser';
        Configure::write('Auth.useModel', 'MockUser');

        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->User;
        $this->assertIsA($result, 'MockUser');
    }

    // Saving logout and login url corretcly
    function testLoginAndLogoutUrlSaved() {
        $this->CaracoleAuth->initialize($this->controller);

        $result = $this->CaracoleAuth->urlLogin;
        $expected = array_merge(array('controller' => Inflector::tableize($this->CaracoleAuth->modelName), 'plugin' => $this->CaracoleAuth->modelPlugin), array('action' => 'login'));
        $this->assertEqual($result, $expected);

        $result = $this->CaracoleAuth->urlLogout;
        $expected = array_merge(array('controller' => Inflector::tableize($this->CaracoleAuth->modelName), 'plugin' => $this->CaracoleAuth->modelPlugin), array('action' => 'logout'));
        $this->assertEqual($result, $expected);
    }

    // Startup stops when installing
    function testStopsStartupWhenInstalling() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->controller->name = 'Installs';
        $result = $this->CaracoleAuth->startup($this->controller);
        $this->assertTrue($result);
    }

    // Default status is not loggued in
    function testDefaultStatusIsNotLogguedIn() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->startup($this->controller);
        $result = $this->CaracoleAuth->activeUser['is_loggued'];
        $this->assertFalse($result);
    }

   // Logging on startup if id in session
    function testLoginAtStartupWhenIdInSession() {
        $this->controller->Session->write('Auth.activeUser', 1);
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->startup($this->controller);
        $result = $this->CaracoleAuth->activeUser['is_loggued'];
        $this->assertTrue($result);
    }

    // Returns true if action allowed
    function testActionAllowedAtStartup() {
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->startup($this->controller);
        $this->assertTrue($result);
    }

    // Redirection if action not allowed at startup
    function testRedirectingIfNotAllowedAtStartup() {
        $this->controller->Session->write('Auth.activeUser', 4);
        $this->controller->action = 'admin_index';
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->startup($this->controller);
        $this->assertEqual($this->controller->redirectUrl, $this->CaracoleAuth->urlLogin);
    }

    // Accept action if in whitelist
    function testAcceptActionIfInWhiteList() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->whiteList = array('admin_whitelist');
        $this->controller->action = 'admin_whitelist';
        $result = $this->CaracoleAuth->actionAllowed();
        $this->assertTrue($result);
    }

    // Accept if all actions are allowed
    function testAcceptActionIfAllActionAllowed() {
        $this->CaracoleAuth->initialize($this->controller);
        $user = array('User' => array('acl' => '*:*'));
        $this->CaracoleAuth->accessRights = array('is_admin' => null, 'is_member' => null, 'default' => null);
        $controller = 'controller';
        $action = 'action';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertTrue($result);
    }

    // Not allowed if all actions are not allowed
    function testRefuseActionIfAllActionForbidden() {
        $this->CaracoleAuth->initialize($this->controller);
        $user = array('User' => array('acl' => '!*:*'));
        $this->CaracoleAuth->accessRights = array('is_admin' => null, 'is_member' => null, 'default' => null);
        $controller = 'controller';
        $action = 'action';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);
    }

    // Forbid one specific action
    function testForbidSpecificAction() {
        $this->CaracoleAuth->initialize($this->controller);
        $user = array('User' => array('acl' => '*:*,!*:foo'));
        $controller = 'controller';

        $action = 'bar';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertTrue($result);

        $action = 'foo';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);
    }

    // Forbid all actions of a given controller
    function testForbidSpecificController() {
        $this->CaracoleAuth->initialize($this->controller);
        $user = array('User' => array('acl' => '!foo:*'));
        $action = 'action';

        $controller = 'bar';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertTrue($result);

        $controller = 'foo';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);
    }

    // One action forbidden on all controllers
    function testOneActionForbiddenOnAllControllers() {
        $this->CaracoleAuth->initialize($this->controller);
        $user = array('User' => array('acl' => '!*:foo'));
        $controller = 'foo';
        $action = 'foo';

        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);

        $controller = 'bar';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);
    }

    // Only one controller/action combination allowed
    function testOnlyOneControllerAndActionCombinationAllowed() {
        $this->CaracoleAuth->initialize($this->controller);
        $user = array('User' => array('acl' => '!*:*,foo:bar'));

        $controller = 'foo';
        $action = 'foo';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);

        $controller = 'bar';
        $action = 'bar';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);

        $controller = 'foo';
        $action = 'bar';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertTrue($result);

        $controller = 'bar';
        $action = 'bar';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);
    }

    // Forbid all actions starting with admin_
    function testForbidAllActionPrefixedWithAdmin() {
        $this->CaracoleAuth->initialize($this->controller);
        $user = array('User' => array('acl' => '!*:admin_*'));
        $controller = 'controller';

        $action = 'admin_foo';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);

        $action = 'admin_bar';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);
    }

    // Forbidding all admin_ and member_ prefixed actions but allowing others
    function testForbidAllAdminAndMemberPrefixedActions() {
        $this->CaracoleAuth->initialize($this->controller);
        $user = array('User' => array('acl' => '*:*,!*:admin_*,!*:member_*'));
        $controller = 'controller';

        $action = 'admin_foo';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);

        $action = 'member_foo';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertFalse($result);

        $action = 'foo';
        $result = $this->CaracoleAuth->actionAllowed($controller, $action, $user);
        $this->assertTrue($result);
    }

    // Session cleared when logging out
    function testSessionClearedOnLogout() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->logout();
        $result = $this->controller->Session->check('Auth.activeUser');
        $this->assertFalse($result);
    }

    // Cookie cleared when logging out
    function testCookieClearedOnLogout() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->logout();
        $result = $this->controller->Cookie->read('Auth.activeUser');
        $this->assertFalse($result);
    }


    // Configure active user not loggued in when logging out
    function testConfigureActiveUserNotLogguedInWhenLoggingOut() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->logout();
        $result = Configure::read('Auth.activeUser');
        $this->assertFalse($result['is_loggued']);
    }

    // CaracoleAuth active user not loggued in when logging out
    function testCaracoleAuthActiveUserNotLogguedInWhenLoggingOut() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->logout();
        $result = $this->CaracoleAuth->activeUser;
        $this->assertFalse($result['is_loggued']);
    }

    // active user view variable not loggued in when logging out
    function testActiveUserViewVariableNotLogguedInWhenLoggingOut() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->logout();
        $result = $this->controller->viewVars['activeUser'];
        $this->assertFalse($result['is_loggued']);
    }

    // Loggued in if the active user is_loggued key is true
    function testIsLogguedInTrue() {
        Mock::generatePartial('CaracoleAuthComponent', 'MockCaracoleAuthActiveUser', array('getActiveUser'));
        $this->CaracoleAuth = new MockCaracoleAuthActiveUser();
        $this->CaracoleAuth->setReturnValue('getActiveUser', array('is_loggued' => true));
        $result = $this->CaracoleAuth->isLogguedIn();
        $this->assertTrue($result);
    }

    // Not loggued in is the active user is_loggued key is false
    function testIsLogguedInFalse() {
        $this->CaracoleAuth = $this->mockCaracoleAuth;
        $this->CaracoleAuth->setReturnValue('getActiveUser', array('is_loggued' => false));
        $result = $this->CaracoleAuth->isLogguedIn();
        $this->assertFalse($result);
    }

    // Fetching active user from component cache first
    function testGetActiveUserFromComponentFirst() {
        $this->controller->Session->write('Auth.activeUser', 2);
        $this->controller->Cookie->write('Auth.activeUser', '1:testtoken');
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->activeUser = array('User' => array('id' => 3));
        $result = $this->CaracoleAuth->getActiveUser();
        $this->assertEqual($result['User']['id'], 3);
    }

    // Force restting component cache to fetch the active user
    function testForceSkippingComponentCacheToGrabActiveUser() {
        $this->controller->Session->write('Auth.activeUser', 2);
        $this->controller->Cookie->write('Auth.activeUser', '1:testtoken');
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->activeUser = array('User' => array('id' => 3));
        $result = $this->CaracoleAuth->getActiveUser(true);
        $this->assertEqual($result['User']['id'], 2);
    }

    // Getting user from Session if cache is empty
    function testFindActiveUserFromSessionIfComponentCacheIsEmpty() {
        $this->controller->Session->write('Auth.activeUser', 2);
        $this->controller->Cookie->write('Auth.activeUser', '1:testtoken');
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->getActiveUser();
        $this->assertEqual($result['User']['id'], 2);
    }

    // Getting user from Cookie if cache and session are empty
    function testFindActiveUserFromCookieIfBothCacheAndSessionAreEmpty() {
        $this->controller->Cookie->write('Auth.activeUser', '1:testtoken');
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->getActiveUser();
        $this->assertEqual($result['User']['id'], 1);
    }

	 // Getting default user if no previous user is found
    function testGetDefaultUserIfNoPreviousUserIsFound() {
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->getActiveUser();
        $this->assertFalse($result['is_loggued']);
    }

    // Setting default user as active user if no data found
    function testSetActiveUserAsDefaultOneByDefault() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->setActiveUser();
        $result = $this->CaracoleAuth->activeUser;
        $expected = $this->CaracoleAuth->__getDefaultUser();
        $this->assertEqual($result, $expected);
    }

    // Setting active user to the one specified in id
    function testSetActiveUserToTheOneSpecifiedInArray() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->setActiveUser(1);
        $result = $this->CaracoleAuth->activeUser;
        $expected = $this->CaracoleAuth->User->find('first', array('conditions' => array('id' => 1)));
        $this->assertEqual($result['User'], $expected['User']);
    }

    // Write active user in configure
    function testActiveUserWrittenInConfigure() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->setActiveUser(1);
        $result = Configure::read('Auth.activeUser');
        $expected = $this->CaracoleAuth->User->find('first', array('conditions' => array('id' => 1)));
        $this->assertEqual($result['User'], $expected['User']);
    }

    // Active user saved in view variable
    function testActiveUserSavedInViewVariable() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->setActiveUser(1);
        $result = $this->controller->viewVars['activeUser'];
        $expected = $this->CaracoleAuth->User->find('first', array('conditions' => array('id' => 1)));
        $this->assertEqual($result['User'], $expected['User']);
    }

    // Writing active user token in cookie when persisting
    function testWritePassTokenToCookieWhenPersisting() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->controller->Cookie->write('Auth.activeUser', 'foo');
        $this->CaracoleAuth->persist(1);

        $result = $this->controller->Cookie->read('Auth.activeUser');
        $this->assertNotEqual($result, 'foo');
        list($id, $token) = explode(':', $result);
        $this->assertEqual($id, 1);
        $this->assertNotNull($token);
    }

    // Saving a login token in database that match the one saved in the cookie
    function testSaveLoginTokenInDatabaseThatMatchCookieToken() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->User->UserLoginToken->deleteAll(array('user_id' => 1));
        $this->CaracoleAuth->persist(1);

        $result = $this->controller->Cookie->read('Auth.activeUser');
        list($id, $token) = explode(':', $result);
        $result = $this->CaracoleAuth->User->UserLoginToken->find('count', array(
            'UserLoginToken.token' => Security::hash($token, null, true)
        ));
        $this->assertEqual($result, 1);
    }

    // Default user has all sensitive fields empty
    function testDefaultUserHasAllSensitiveFieldsEmpty() {
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->__getDefaultUser();
        $this->assertFalse($result['User']['id']);
        $this->assertFalse($result['User']['is_admin']);
        $this->assertFalse($result['User']['is_member']);
        $this->assertFalse($result['User']['is_master']);
        $this->assertFalse($result['User']['is_disabled']);
        $this->assertFalse($result['User']['acl']);
        $this->assertFalse($result['is_loggued']);
    }

    // Grabbing user from cookie returns false if no cookie found
    function testGettingUserFromCookieFailsIfNoCorrectCookie() {
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->__getUserFromCookie();
        $this->assertFalse($result);
    }

    // Can't get user from cookie if the cookie does not hold a token
    function testCantGetUserFromCookieIfCookieDoesNotContainAToken() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->controller->Cookie->write('Auth.activeUser', 'foo');
        $result = $this->CaracoleAuth->__getUserFromCookie();
        $this->assertFalse($result);
    }

    // Can't login from an expired token
    function testExpiredTokenCantAllowLogin() {
	    $this->controller->Cookie->write('Auth.activeUser', '1:testtoken');
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->User->UserLoginToken->deleteAll(array('user_id' => 1));
        $this->CaracoleAuth->persist(1);

        // Setting the token as expired
        $tokenResult = $this->CaracoleAuth->User->UserLoginToken->find('first');
        $this->CaracoleAuth->User->UserLoginToken->create($tokenResult);
        $this->CaracoleAuth->User->UserLoginToken->saveField('expires', date('Y-m-d H:i:s', strtotime('-1 day')));

        $result = $this->CaracoleAuth->__getUserFromCookie();
        $this->assertFalse($result);
    }

    // Trying to log from a non-existent token should delete all other tokens of the same user as a preventive measure
    function testBadTokenWillDeleteAllTokensOfUser() {
		$this->controller->Cookie->write('Auth.activeUser', '1:badtoken');

		$this->CaracoleAuth->initialize($this->controller);

        // Do not log in
		$result = $this->CaracoleAuth->__getUserFromCookie();
		$this->assertFalse($result);
        // Deletes all other tokens
		$result = $this->CaracoleAuth->User->UserLoginToken->find('count', array('conditions' => array('UserLoginToken.user_id' => 1)));
        $this->assertFalse($result);
    }

    // Can't login from a token that do not belongs to the user
    function testNotOwnTokenCantAllowLogin() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->User->UserLoginToken->deleteAll(array('user_id' => 1));
        $this->CaracoleAuth->persist(1);

        // Setting the token as belonging to another user
        $tokenResult = $this->CaracoleAuth->User->UserLoginToken->find('first');
        $this->CaracoleAuth->User->UserLoginToken->create($tokenResult);
        $this->CaracoleAuth->User->UserLoginToken->saveField('user_id', 2);

        $result = $this->CaracoleAuth->__getUserFromCookie();
        $this->assertFalse($result);
    }

    // Getting correct user from valid token
    function testGettingCorrectUserFromValidToken() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->User->UserLoginToken->deleteAll(array('user_id' => 1));
        $this->CaracoleAuth->persist(1);

        $result = $this->CaracoleAuth->__getUserFromCookie();
        $this->assertEqual($result['User']['id'], 1);
    }

    // Deleting used tokens
    function testDeletingUsedTokens() {
		 // We first delete all token
	    $this->CaracoleAuth->initialize($this->controller);
		$this->CaracoleAuth->User->UserLoginToken->deleteAll(array('user_id' => 1));

		// We persist user 1, creating a cookie and a UserLoginToken
		$this->CaracoleAuth->persist(1);
	   // We get that cookie value and the corresponding hashed token
		$cookieValue = $this->controller->Cookie->read('Auth.activeUser');
        list(,$token) = explode(':', $cookieValue);
		$hashedToken = Security::hash($token, null, true);

		// We read the user from the cookie
		$result = $this->CaracoleAuth->__getUserFromCookie();
		// It will log him in, updating the cookie and deleting the used UserLoginToken
		$result = $this->CaracoleAuth->User->UserLoginToken->find('first', array('conditions' => array('UserLoginToken.token' => $hashedToken)));
        $this->assertFalse($result);
    }

    // Re-calling the persist feature whenever the login from cookie is called
    function testPersistAgainOnceLogguedFromCookie() {
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->User->UserLoginToken->deleteAll(array('user_id' => 1));
        $this->CaracoleAuth->persist(1);

        Mock::generatePartial('CaracoleAuthComponent', 'MockCaracoleAuthComponentPersistOnceAgain', array('persist'));
        $this->CaracoleAuth = new MockCaracoleAuthComponentPersistOnceAgain();
        $this->CaracoleAuth->initialize($this->controller);
        $this->CaracoleAuth->expectOnce('persist', array('1'));

        $this->CaracoleAuth->__getUserFromCookie();
    }

    // Can't get user from database if no id given
    function testNoUserFromDatabaseIfNoIdGiven() {
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->__getUserFromDatabase();
        $this->assertFalse($result);
    }

    // Not loggued in user if specified user does not exists
    function testNotLogguedInIfNonExistentUser() {
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->__getUserFromDatabase(42);
        $this->assertFalse($result['is_loggued']);
    }

    // Loggued in user if user exists
    function testLogguedInIfExistentUser() {
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->__getUserFromDatabase(1);
        $this->assertTrue($result['is_loggued']);
    }

    // Use special configure conditions to fetch user
    function testUseSpecialAuthSelectionOptionsToFetchUser() {
        Configure::write('Auth.selectOptions', array('conditions' => array('User.is_disabled' => array(0, 1))));
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->__getUserFromDatabase(5);
        $this->assertTrue($result['is_loggued']);
    }

    // Can't get no user from Session if Session is empty
    function testGetNoUserFromSessionIfSessionEmpty() {
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->__getUserFromSession();
        $this->assertFalse($result);
    }

    // Get the user set in session if session is set
    function testGetUserInSession() {
        $this->controller->Session->write('Auth.activeUser', 1);
        $this->CaracoleAuth->initialize($this->controller);
        $result = $this->CaracoleAuth->__getUserFromSession();
        $this->assertEqual($result['User']['id'], 1);
        $this->assertTrue($result['is_loggued']);
    }





}
