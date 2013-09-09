<?php
Configure::write('Auth.useModel', 'CaracoleUsers.User');
Configure::write('Auth.modelAlias', 'User');
Configure::write('Auth.selectOptions', array());
App::import('Controller', 'CaracoleUsers.Users');
// Overrideing the UserController
class UserTestController extends UsersController {
    var $name = 'Users';
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
	function cakeError($error) {
		$this->error = $error;
	}

}
// Dummy class
class DummyTest {}



class UsersControllerTestCase extends CakeTestCase {
    var $fixtures = array(
        'plugin.caracole_users.user',
        'plugin.caracole_users.user_login_token',
        'plugin.caracole_users.user_pass_token'
    );


    function startTest() {
        // Controller
        $this->controller = new UserTestController();
		$this->controller->constructClasses();
		$this->controller->Component->initialize($this->controller);
		$this->controller->beforeFilter();
		$this->controller->Component->startup($this->controller);

        Mock::generate('OpenidComponent');
        Mock::generate('SessionComponent');
        Mock::generate('CaracoleAuthComponent');
		Mock::generate('EmailComponent');
		Mock::generate('User');
		Mock::generate('UserLoginToken');
		Mock::generate('UserPassToken');

		$this->clearMockComponents();
		$this->clearMockModels();

		$this->controller->Openid = &$this->openid;
        $this->controller->Session = &$this->session;
        $this->controller->CaracoleAuth = &$this->caracoleAuth;
		$this->controller->Email = &$this->email;
		$this->controller->model = &$this->model;
		$this->controller->model->UserLoginToken = &$this->userLoginToken;
		$this->controller->model->UserPassToken = &$this->userPassToken;

        //$this->controller->model = & ClassRegistry::init('User');

        // Counting number of calls
        $this->count = array(
          'Openid.isResponse' => 0,
          'Openid.getUser' => 0,
          'Openid.error' => 0,

          'Session.check' => 0,
          'Session.read' => 0
        );
	}

    function clearMockComponents() {
        $this->openid = new MockOpenidComponent();
        $this->session = new MockSessionComponent();
        $this->caracoleAuth = new MockCaracoleAuthComponent();
		$this->email = new MockEmailComponent();
    }

	function clearMockModels() {
        $this->model = new MockUser();
		$this->model->alias = 'User';
		$this->model->Behaviors = array();
		$this->model->adminSettings = array(
			'fields' => array()
		);
        $this->userLoginToken = new MockUserLoginToken();
		$this->model->UserLoginToken = &$this->userLoginToken;
        $this->userPassToken = new MockUserPassToken();
		$this->model->UserPassToken = &$this->userPassToken;
    }

    /**
     *  testLogin
     **/
    function testLogin() {
		$this->clearMockComponents();
        // An open id response is found
        $this->openid->setReturnValue('isResponse', true);
            // but no corresponding user match : we render and set a flash message
            $this->openid->setReturnValue('getUser', false);
            $this->session->expectOnce('setFlash');
            $this->controller->renderedAction = 'foo';
                $this->controller->login();
                    $this->assertNull($this->controller->renderedAction);

            // with a matching user
                $this->clearMockComponents();
                $this->openid->setReturnValue('isResponse', true);
				$this->openid->setReturnValue('getUser', array('User' => array('id' => 1)));
                $this->caracoleAuth->expectOnce('setActiveUser');
                    $this->controller->login();

                // enable the persistent feature if such a key is set in the session
                $this->clearMockComponents();
                $this->openid->setReturnValue('isResponse', true);
                $this->openid->setReturnValue('getUser', array('User' => array('id' => 1)));
                $this->session->setReturnValue('check', true, array('Auth.persistOpenId'));
                $this->session->setReturnValue('read', true, array('Auth.persistOpenId'));
                $this->session->expectOnce('delete', array('Auth.persistOpenId'));
                $this->caracoleAuth->expectOnce('persist');
					$this->controller->login();

                // we finally redirect to the page saved in session
				$this->clearMockComponents();
				$this->openid->setReturnValue('isResponse', true);
                $this->openid->setReturnValue('getUser', array('User' => array('id' => 1)));
				$this->session->setReturnValue('check', false, array('Auth.persistOpenId'));
                $this->session->setReturnValue('read', false, array('Auth.persistOpenId'));
				$this->session->setReturnValue('read', 'requestedPage', array('Auth.requestedPage'));
					$this->controller->login();
                        $this->assertEqual($this->controller->redirectUrl, 'requestedPage');

        // No more open id, we render if no data passed
		$this->clearMockComponents();
		$this->openid->setReturnValue('isResponse', false);
		$this->controller->data = null;
		$this->controller->renderedAction = 'foo';
			$result = $this->controller->login();
				$this->assertNull($this->controller->renderedAction);


        // Data passed with an open id key, we save it in session and return true if authenticate
		$this->clearMockComponents();
		$this->openid->setReturnValue('isResponse', false);
		$this->controller->data = array('User' => array('openid' => 'http://tim.openid.com'), 'Options' => array('is_remember' => false));
		$this->session->expectOnce('write', array('Auth.persistOpenId', false));
		$this->openid->setReturnValue('authenticate', true);
			$result = $this->controller->login();
				$this->assertTrue($result);

            // Does not authenticate : we set a flash message and render it
			$this->clearMockComponents();
			$this->openid->setReturnValue('isResponse', false);
			$this->controller->data = array('User' => array('openid' => 'http://tim.openid.com'), 'Options' => array('is_remember' => false));
			$this->openid->setReturnValue('authenticate', false);
			$this->session->expectOnce('setFlash');
			$this->controller->renderedAction = 'foo';
				$this->controller->login();
					$this->assertNull($this->controller->renderedAction);

        // Setting the active user in CaracoleAuth
		$this->clearMockComponents();
		$this->openid->setReturnValue('isResponse', false);
		$this->controller->data = array('User' => array('name' => 'tim@pixelastic', 'password' => null));
		$this->caracoleAuth->expectOnce('setActiveUser');
            // But setting a flash message and rendering if not loggued in
			$this->caracoleAuth->setReturnValue('isLogguedIn', false);
			$this->controller->renderedAction = 'foo';
			$this->session->expectOnce('setFlash');
				$this->controller->login();
					$result = $this->controller->renderedAction;
					$this->assertNull($result);

        // Calling the CaracoleAuth persist feature if is_remember is passed and redirecting to the page in session
		$this->clearMockComponents();
		$this->openid->setReturnValue('isResponse', false);
		$this->controller->data = array('User' => array('name' => 'tim@pixelastic', 'password' => null), 'Options' => array('is_remember' => true));
		$this->caracoleAuth->setReturnValue('isLogguedIn', true);
		$this->caracoleAuth->expectOnce('persist');
		$this->session->setReturnValue('read', 'requestedPage', array('Auth.requestedPage'));
		$this->controller->redirectUrl = null;
			$this->controller->login();
				$this->assertEqual($this->controller->redirectUrl, 'requestedPage');
    }

	/**
	 *	testLogout
	 **/
	function testLogout() {
		// Calling caracoleAuth logout
		$this->clearMockComponents();
		$this->caracoleAuth->expectOnce('logout');
		$this->controller->logout();

		// Redirecting either to SiteUrl.default or to SiteUrl.admin depending if in admin panel or not
		$this->clearMockComponents();
		Configure::write('SiteUrl.admin', 'adminUrl');
		$this->controller->params = array('admin' => true);
		$this->controller->redirectUrl = null;
			$this->controller->logout();
				$this->assertEqual($this->controller->redirectUrl, 'adminUrl');

		Configure::write('SiteUrl.default', 'defaultUrl');
		$this->controller->params = array('admin' => false);
		$this->controller->redirectUrl = null;
			$this->controller->logout();
				$this->assertEqual($this->controller->redirectUrl, 'defaultUrl');

	}

	/**
	 *	testPassStepOne
	 *	Pass token is empty, we only handle the displaying of the form and validation before regenerating a new pass token
	 **/
	function testPassStepOne() {
		$this->clearMockComponents();
		// If data is empty, we render the form
		$this->controller->data = array();
			$this->controller->pass();
				$this->assertNull($this->controller->renderedAction);

		// If data is set, we stop if it does not validate
		$this->clearMockComponents();
		$this->controller->data = array('foo' => 'bar');
		$this->model->setReturnValue('validates', false);
			$this->controller->pass();
				$this->assertNull($this->controller->renderedAction);

		// We delete all expired, used or other token of the same user
		$this->clearMockComponents();
		$this->clearMockModels();
		$this->controller->action = 'foo';
		$this->controller->data = array('User' => array('name' => 'foo'));
		$this->controller->params = array('plugin' => null, 'controller' => null, 'action' => null, 'admin' => null);
		$this->model->id = 1;
		$this->model->data = $this->controller->data;
		$this->model->setReturnValue('validates', true);
		// Setting non-mock object to correctly delete the passtokens
		$this->userPassToken = ClassRegistry::init('UserPassToken');
		$this->model->UserPassToken = &$this->userPassToken;
			$this->controller->pass();
				// We save a new token in the database
				$result = $this->userPassToken->find('count', array('conditions' => array('UserPassToken.user_id' => 1)));
				//debug($this->userPassToken->find('all'));
				$this->assertEqual($result, 1);
				// We delete used and expired tokens
				$result = $this->userPassToken->find('count', array('conditions' => array('UserPassToken.expires <=' => date('Y-m-d H:i:s'))));
				$this->assertFalse($result);
				$result = $this->userPassToken->find('count', array('conditions' => array('UserPassToken.is_used' => 1)));
				$this->assertFalse($result);

		// And make sure that is has a token set
		$result = $this->userPassToken->find('first', array('conditions' => array('UserPassToken.id' => $this->userPassToken->getLastInsertID())));
		$this->assertNotNull($result['UserPassToken']['token']);

		// We make sure that en email is sent
		$this->email->expectOnce('send');

		// And that a url is correctly passed to the view
		$result = $this->controller->viewVars;
		$this->assertNotNull($result['url']);

		// We render the *_sent view
		$result = $this->controller->renderedAction;
		$this->assertEqual($result, 'foo_sent');

	}

	/**
	 *	testPassStep2
	 *	A token is given, if it is valid, we should present the user with a form to change its password
	 **/
	function testPassStepTwo() {


		$this->clearMockComponents();
		$this->clearMockModels();
		$this->userPassToken = ClassRegistry::init('UserPassToken');
		$this->model->UserPassToken = &$this->userPassToken;

		// If the decoded token does not contain a semicolon, we have to stop
		$passToken = base64_encode('1tomorrow');
			$this->controller->pass($passToken);
				$this->assertEqual($this->controller->error, 'error404');

		// If there is no userId given, we also stop
		$passToken = base64_encode(':tomorrow');
			$this->controller->pass($passToken);
				$this->assertEqual($this->controller->error, 'error404');

		// We delete all expired token of that user
		$this->model->UserPassToken->create(array(
			'user_id' => 1,
			'expires' => date('Y-m-d H:i:s', strtotime('-1 day'))
		));
		$this->model->UserPassToken->save(null, false);
		$passToken = base64_encode('1:tomorrow');
			$this->controller->pass($passToken);
				$result = $this->model->UserPassToken->find('count', array('conditions' => array('UserPassToken.user_id' => 1)));
				$this->assertEqual($result, 1);

		// We try to find a match for this user/token. If none is found, we delete all tokens of that user
		$this->clearMockModels();
		$this->userPassToken->setReturnValue('find', false);
		$this->userPassToken->expectAt(1, 'deleteAll', array(array('user_id' => '1')));
		$this->controller->action = 'foo';
		$passToken = base64_encode('1:tomorrow');
			$this->controller->pass($passToken);
				// And render the token error view
				$this->assertEqual($this->controller->renderedAction, 'foo_token_error');

		// if everything is correct, and no data submitted, we display the form
		$this->clearMockModels();
		$this->userPassToken->setReturnValue('find', true);
		$this->controller->action = 'foo';
		$this->controller->data = null;
		$passToken = base64_encode('1:tomorrow');
			$this->controller->pass($passToken);
				$this->assertEqual($this->controller->renderedAction, 'foo_form');

		// Data submitted, we still display the form if it does not validate
		$this->clearMockModels();
		$this->userPassToken->setReturnValue('find', true);
		$this->controller->action = 'foo';
		$this->controller->data = array('foo' => 'bar');
		$this->model->setReturnValue('validates', false);
		$passToken = base64_encode('1:tomorrow');
			$this->controller->pass($passToken);
				$this->assertEqual($this->controller->renderedAction, 'foo_form');

		// If validates, we update the password in the database
		$this->clearMockModels();
		$this->userPassToken->setReturnValue('find', array('UserPassToken' => array('id' => 'passTokenId')));
		$this->controller->action = 'foo';
		$this->controller->data = array('User' => array('password' => 'foo'));
		$this->model->setReturnValue('validates', true);
		$passToken = base64_encode('1:tomorrow');
			$this->controller->pass($passToken);
				// Update the passworw field
				$hashedPassword = Security::hash('foo', null, true);
				$this->model->expectOnce('saveField', array('password', $hashedPassword));
				// Setting as active user
				$this->caracoleAuth->expectOnce('setActiveUser', array('1'));
				// Deleting the corresponding token
				$this->userPassToken->expectOnce('delete', array('passTokenId'));
				// Render the form ok view
				$this->assertEqual($this->controller->renderedAction, 'foo_form_ok');

	}

	/**
	 *	testAdminEdit
	 **/
	function testAdminEdit() {

		// When editing, we set the password and password_confirm tooltips
		$this->clearMockComponents();
		$this->clearMockModels();
		$this->controller->params = array('named' => array());
			$this->controller->admin_edit(1);
				$result = $this->model->adminSettings['fields'];
				$this->assertNotNull($result['password']['help']);
				$this->assertNotNull($result['password_confirm']['help']);

		// If a password is set, it should be hashed
		$this->clearMockModels();
		$this->controller->data = array('User' => array('password' => 'pass', 'password_confirm' => 'pass'));
		$hashedPass = Security::hash('pass', null, true);
		$expected = array('User' => array('password' => $hashedPass, 'password_confirm' => $hashedPass));
		$this->model->expectOnce('create', array($expected));
			$this->controller->admin_edit();

		// If no password is set in edit mode, we remove it and the validation rule
		$this->clearMockModels();
		$this->model->setReturnValue('validates', false);
		$this->model->validate = array('password_confirm' => 'foo');
		$this->controller->data = array('User' => array('password' => '', 'password_confirm' => 'pass'));
		$expected = array('User' => array('password_confirm' => 'pass'));
		$this->model->expectOnce('create', array($expected));
			$this->controller->admin_edit(1);
				$this->assertNull(@$this->model->validate['password_confirm']);

		// When displaying without submitting data, password and confirmation should be empty
		$this->clearMockModels();
		$this->controller->data = array('User' => array('password' => 'pass', 'password_confirm' => 'pass'));
			$this->controller->admin_edit(1);
				$result = $this->controller->data['User'];
				$this->assertFalse($result['password']);
				$this->assertFalse($result['password_confirm']);
	}


}
