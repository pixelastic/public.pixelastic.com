<?php
App::import('Component', 'CaracoleUsers.Openid');

// OpenIdTestController
class OpenIdTestController extends Controller {
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
class OpenIdTest { var $useTable = false; }
// Fake request object
class MockRequest {}



class OpenIdTestCase extends CakeTestCase {
    var $fixtures = array(
        'plugin.caracole_users.user',
        'plugin.caracole_users.user_login_token',
        'plugin.caracole_users.user_pass_token'
    );


    function startTest() {
        // Controller
        $this->controller = new OpenIdTestController();
		$this->controller->constructClasses();
		$this->controller->Component->initialize($this->controller);
		$this->controller->beforeFilter();
		$this->controller->Component->startup($this->controller);

        $this->controller->model = & ClassRegistry::init('User');

        // Component
        $this->Openid = new OpenidComponent();

	}

    /**
     *  testInitialize
     **/
    function testInitialize() {
        // Defining an empty rand source on Windows (passing this test first)
        if (Folder::isWindowsPath(__FILE__)) {
            $_randSource = (defined('Auth_OpenID_RAND_SOURCE')) ? Auth_OpenID_RAND_SOURCE : null;
            $this->Openid->initialize($this->controller);
            $result = Auth_OpenID_RAND_SOURCE;
            $this->assertNull($result);
        }

        // Setting controller as the one passed
        $this->Openid->initialize($this->controller);
        $this->assertEqual($this->Openid->controller, $this->controller);

        // Setting settings as a merge of the second argument and the defaults
        $this->Openid->defaults = array('foo' => 'bar', 'bar' => 'baz');
        $this->Openid->initialize($this->controller, array('foo' => 'baz'));
        $result = $this->Openid->settings;
        $expected = array('foo' => 'baz', 'bar' => 'baz');
        $this->assertEqual($result, $expected);

        // Updating the include_path list to add the path to the vendors
        $_includePath = ini_get('include_path');
        $_vendorPath = App::pluginPath('CaracoleUsers').'vendors'.DS;
        $this->Openid->initialize($this->controller);
        $result = ini_get('include_path');
        $this->assertTrue(strpos($result, $_vendorPath)!==false);
        $this->assertTrue(strpos($result, $_vendorPath.'pear'.DS));
    }

    /**
     *  test__authenticate
     **/
    function test__authenticate() {
        // Creating openid mock object
        Mock::generatePartial('OpenidComponent', 'MockOpenidComponentAuthenticate', array('__getRequest', 'isOpenIdFailure', 'shouldSendRedirect', 'displayFormRedirect'));
        $this->Openid = new MockOpenidComponentAuthenticate();
        $this->Openid->initialize($this->controller);
        $this->Openid->__getConsumer();

        // Mock request object
        Mock::generatePartial('MockRequest', 'MockRequestAuthenticate', array('addExtension', 'redirectUrl', 'shouldSendRedirect'));
        $request = new MockRequestAuthenticate();

        // Keeping count of calls
        $openIdCount = array('__getRequest' => 0, 'isOpenIdFailure' => 0, 'shouldSendRedirect' => 0, 'displayFormRedirect' => 0);
        $requestCount = array('addExtension' => 0, 'redirectUrl' => 0, 'shouldSendRedirect' => 0);

        // Return an error if can't get the request or if the request is empty
        $this->Openid->setReturnValueAt($openIdCount['__getRequest']++, '__getRequest', null);
        $result = $this->Openid->authenticate('url');
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);
        $this->Openid->error = null;
        $this->Openid->setReturnValueAt($openIdCount['__getRequest']++, '__getRequest', false);
        $result = $this->Openid->authenticate('url');
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // Return an error if the redirect url is an openId failure
        $this->Openid->setReturnValueAt($openIdCount['isOpenIdFailure']++, 'isOpenIdFailure', true);
        $this->Openid->setReturnValueAt($openIdCount['__getRequest']++, '__getRequest', &$request);
        $redirectUrl = new OpenIdTest();
        $redirectUrl->message = 'foo';
        $request->setReturnValueAt($requestCount['redirectUrl']++, 'redirectUrl', &$redirectUrl);
        $this->Openid->error = null;
        $result = $this->Openid->authenticate('url');
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // Redirect to the redirect url if shouldSendRedirect()
        $this->Openid->setReturnValueAt($openIdCount['isOpenIdFailure']++, 'isOpenIdFailure', false);
        $this->Openid->setReturnValueAt($openIdCount['__getRequest']++, '__getRequest', &$request);
        $request->setReturnValueAt($requestCount['redirectUrl']++, 'redirectUrl', 'redirectUrl');
        $request->setReturnValueAt($requestCount['shouldSendRedirect']++, 'shouldSendRedirect', true);
        $result = $this->Openid->authenticate('url');
        $this->assertTrue($result);
        $result = $this->controller->redirectUrl;
        $this->assertEqual($result, 'redirectUrl');

        // Or false if can't displayFormRedirect
        $this->Openid->error = 'error';
        $this->Openid->setReturnValueAt($openIdCount['isOpenIdFailure']++, 'isOpenIdFailure', false);
        $this->Openid->setReturnValueAt($openIdCount['__getRequest']++, '__getRequest', &$request);
        $request->setReturnValueAt($requestCount['shouldSendRedirect']++, 'shouldSendRedirect', false);
        $this->Openid->setReturnValueAt($openIdCount['displayFormRedirect']++, 'displayFormRedirect', false);
        $result = $this->Openid->authenticate('url');
        $this->assertEqual($result, 'error');


    }


    /**
     *  testIsResponse
     **/
    function testIsResponse() {
        $this->Openid->initialize($this->controller);

        // True if an openid_ns param in the url
        $_GET = array();
        $response = $this->Openid->isResponse();
        $this->assertFalse($response);
        $_GET = array('foo' => 'bar', 'bar' => 'baz');
        $this->controller->params = array('url' => array('openid_ns' => 'foo'));
        $response = $this->Openid->isResponse();
        $this->assertTrue($response);

    }

     /**
	 *	testGetUser
	 **/
	function testGetUser() {
        // Creating openid mock object
        Mock::generatePartial('OpenidComponent', 'MockOpenidComponentGetUser', array('__getResponse', '__parseUserFromResponse'));
        $this->Openid = new MockOpenidComponentGetUser();
        $this->Openid->initialize($this->controller);
        $this->Openid->__getConsumer();
        $_getResponseCount = 0;
        $_parseUserFromResponseCount = 0;

        // Creating fake response
        $response = new OpenIdTest();
        $parsedResponse = array();

        // Stops with error if the response is an Auth_OpenID_CANCEL
        $response->status = Auth_OpenID_CANCEL;
        $this->Openid->setReturnValueAt($_getResponseCount++, '__getResponse', &$response);
        $result = $this->Openid->getUser();
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // Stops with error if the response is a Auth_OpenID_FAILURE
        $response->status = Auth_OpenID_FAILURE;
        $response->message = 'foo';
        $this->Openid->setReturnValueAt($_getResponseCount++, '__getResponse', &$response);
        $result = $this->Openid->getUser();
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // Returns a matching user if one is found
        $response->status = null;
        $response->identity_url = 'http://tim.openid.com';
        $this->Openid->setReturnValueAt($_getResponseCount++, '__getResponse', &$response);
        $result = $this->Openid->getUser();
        $this->assertTrue($result);
        $this->assertEqual($result['User']['id'], 1);

        // If no create and no fallback mail, we return an error
        $response->status = null;
        $response->identity_url = 'http://nonexistent.openid.com';
        $this->Openid->setReturnValueAt($_getResponseCount++, '__getResponse', &$response);
        $this->Openid->setReturnValueAt($_parseUserFromResponseCount++, '__parseUserFromResponse', &$parsedResponse);
        $options = array('fallbackToMail' => false, 'create' => false);
        $result = $this->Openid->getUser($options);
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // if no fallback mail, create and no name, we return an error
        $response->status = null;
        $response->identity_url = 'http://nonexistent.openid.com';
        $this->Openid->setReturnValueAt($_getResponseCount++, '__getResponse', &$response);
        $this->Openid->setReturnValueAt($_parseUserFromResponseCount++, '__parseUserFromResponse', &$parsedResponse);
        $options = array('fallbackToMail' => false, 'create' => true);
        $result = $this->Openid->getUser($options);
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // Mail fallback and name of an existing user already having an openid : do nothing
        $response->status = null;
        $response->identity_url = 'http://nonexistent.openid.com';
        $parsedResponse = array('name' => 'member@pixelastic.com');
        $this->Openid->setReturnValueAt($_getResponseCount++, '__getResponse', &$response);
        $this->Openid->setReturnValueAt($_parseUserFromResponseCount++, '__parseUserFromResponse', &$parsedResponse);
        $options = array('fallbackToMail' => true, 'create' => false);
        $result = $this->Openid->getUser($options);
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // Mail fallback and name of an existing user that don't have an openid : save the openid and return the user
        $response->status = null;
        $response->identity_url = 'http://nonexistent.openid.com';
        $parsedResponse = array('name' => 'nothing@pixelastic.com', 'openid' => $response->identity_url);
        $this->Openid->setReturnValueAt($_getResponseCount++, '__getResponse', &$response);
        $this->Openid->setReturnValueAt($_parseUserFromResponseCount++, '__parseUserFromResponse', &$parsedResponse);
        $options = array('fallbackToMail' => true, 'create' => false);
        $result = $this->Openid->getUser($options);
        $this->assertEqual($result['User']['name'], 'nothing@pixelastic.com');
        $result = $this->controller->model->find('first', array('conditions' => array('User.name' => 'nothing@pixelastic.com')));
        $this->assertEqual($result['User']['openid'], 'http://nonexistent.openid.com');
        $this->controller->model->create($result);
        $this->controller->model->saveField('openid', null);

        // No fallback mail but create with no name : error
        $response->status = null;
        $response->identity_url = 'http://nonexistent.openid.com';
        $parsedResponse = array();
        $this->Openid->setReturnValueAt($_getResponseCount++, '__getResponse', &$response);
        $this->Openid->setReturnValueAt($_parseUserFromResponseCount++, '__parseUserFromResponse', &$parsedResponse);
        $options = array('fallbackToMail' => false, 'create' => true);
        $result = $this->Openid->getUser($options);
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // No fallback mail but create with a name : save and return new user with given openid and name
        $response->status = null;
        $response->identity_url = 'http://nonexistent.openid.com';
        $parsedResponse = array('name' => 'newone@pixelastic.com','openid' => $response->identity_url);
        $this->Openid->setReturnValueAt($_getResponseCount++, '__getResponse', &$response);
        $this->Openid->setReturnValueAt($_parseUserFromResponseCount++, '__parseUserFromResponse', &$parsedResponse);
        $options = array('fallbackToMail' => false, 'create' => true);
        $result = $this->Openid->getUser($options);
        $this->assertEqual($result['User']['name'], 'newone@pixelastic.com');
        $result = $this->controller->model->find('first', array('conditions' => array('User.name' => 'newone@pixelastic.com')));
        $this->assertEqual($result['User']['openid'], 'http://nonexistent.openid.com');
	}


    /**
     *  testError
     **/
    function testError() {
        $this->Openid->initialize($this->controller);

        // Getter : return the current error
        $this->Openid->error = 'foo';
        $result = $this->Openid->error();
        $this->assertEqual($result, 'foo');

        // Setter : set the error and return false
        $result = $this->Openid->error('bar');
        $this->assertEqual($this->Openid->error, 'bar');
        $this->assertFalse($result);
    }


    /**
     *  testDisplayFormRedirect
     **/
    function testDisplayFormRedirect() {
        $this->Openid->initialize($this->controller);
        $this->Openid->__getConsumer();
        $options = array('realm' => 'realm', 'return' => 'return');


        // Error if the form markup failed
        Mock::generatePartial('OpenIdTest', 'MockRequestMarkupFail', array('formMarkup'));
        $mockRequest = new MockRequestMarkupFail();
        $openIdFailure = new Auth_OpenID_FailureResponse(null);
        $mockRequest->setReturnValue('formMarkup', $openIdFailure);
        $this->Openid->error = null;
        $result = $this->Openid->displayFormRedirect($mockRequest, $options);
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // Setting title_for_layout, formId and formHtml if succeeded
        Mock::generatePartial('OpenIdTest', 'MockRequestMarkupSucceed', array('formMarkup'));
        $mockRequest = new MockRequestMarkupSucceed();
        $mockRequest->setReturnValue('formMarkup', 'formHtml');
        $this->Openid->error = null;
        $a = $this->Openid->displayFormRedirect($mockRequest, $options);
        $result = $this->controller->viewVars;
        $this->assertNotNull($result['title_for_layout']);
        $this->assertEqual($result['formId'], 'openid_message');
        $this->assertEqual($result['formHtml'], 'formHtml');

        // Rendering openid_redirect
        $result = $this->controller->renderedAction;
        $this->assertEqual($result, 'openid_redirect');
    }

    /**
     *  testIsOpenIdFailure
     **/
    function testIsOpenIdFailure() {
        // True if it's a Auth_OpenID_FailureResponse
        $thing = new Auth_OpenID_FailureResponse(null);
        $result = $this->Openid->isOpenIdFailure($thing);
        $this->assertTrue($result);

        // False otherwise
        $thing = new OpenIdTest();
        $result = $this->Openid->isOpenIdFailure($thing);
        $this->assertFalse($result);
    }

    /**
     *  test__getConsumer
     **/
    function test__getConsumer() {
        $this->Openid->initialize($this->controller);

        // If consumer already set, return it
        $this->Openid->consumer = 'foo';
        $result = $this->Openid->__getConsumer();
        $this->assertEqual($result, 'foo');

        // Create and return a consumer otherwise
        $this->Openid->consumer = null;
        $result = $this->Openid->__getConsumer();
        $expected = 'Auth_OpenID_Consumer';
        $this->assertIsA($result, $expected);
        $this->assertEqual($result, $this->Openid->consumer);

        // TODO : How to test if the GApps_OpenID_Discovery has been loaded ? It most surely has been already imported

    }

    /**
     *  test__getStore
     **/
    function test__getStore() {
        $this->Openid->initialize($this->controller);

        $databaseStore = $this->Openid->__getDatabaseStore();
        $fileStore = $this->Openid->__getFileStore();

        // Returning store if already set
        $this->Openid->store = $databaseStore;
        $result = $this->Openid->__getStore();
        $this->assertEqual($result, $this->Openid->store);

        // Setting and returning a store otherwise. Based on the store setting either a database or file
        $this->Openid->store = null;
        $this->Openid->settings['store'] = 'database';
        $result = $this->Openid->__getStore();
        $this->assertEqual($result, $this->Openid->store);
        $this->assertEqual($result, $databaseStore);
        $this->Openid->store = null;
        $this->Openid->settings['store'] = 'store';
        $result = $this->Openid->__getStore();
        $this->assertEqual($result, $fileStore);
    }


    /**
     *  test__getDatabaseStore
     **/
    function test__getDatabaseStore() {
        $this->Openid->initialize($this->controller);
        $_settings = $this->Openid->settings;

        // Returning error if PEAR throws an error because the database can't be accessed
        $this->Openid->error = null;
        //              Creating a new database connection that will not connect
        $connectionManagerInstance = ConnectionManager::getInstance();
        $databaseConfig = &$connectionManagerInstance->_dataSources;
        $databaseConfig['foo'] = $databaseConfig['test'];
        $databaseConfig['foo']->config['password'].= 'pass';
        $this->Openid->settings['databaseConfig'] = 'foo';
        $result = $this->Openid->__getDatabaseStore();
        $this->assertFalse($result);
        $this->assertNotNull($this->Openid->error);

        // Returning a new MysqlStore
        $this->Openid->settings = $_settings;
        $result = $this->Openid->__getDatabaseStore();
        $this->assertIsA($result, "Auth_OpenID_MySQLStore");
    }


	/**
     * test__getFileStore
     **/
    function test__getFileStore() {
        $this->Openid->initialize($this->controller);

        // A filestore is created if the file exists
        $storePath = TMP.'openid';
        if (file_exists($storePath) || mkdir($storePath)) {
            $result = $this->Openid->__getFileStore();
            $this->assertIsA($result, 'Auth_OpenID_FileStore');
        }
    }

    /**
     *  test__getQuery
     **/
    function test__getQuery() {
        $this->Openid->initialize($this->controller);

        // Will return the actual query array with the url part stripped
        $_SERVER['QUERY_STRING'] = 'foo=bar&url=baz&baz=bar';
        $result = $this->Openid->__getQuery();
        $expected = array('foo' => 'bar', 'baz' => 'bar');
        $this->assertEqual($result, $expected);

    }

    /**
     *  test__parseUserFromResponse
     **/
    function test__parseUserFromResponse() {
        Mock::generatePartial('OpenidComponent', 'MockOpenidComponent__ParseUserFromResponse', array('__parseSRegResponse', '__parseAXResponse', '__getSRegResponse', '__getAXResponse'));
        $mockOpenid = new MockOpenidComponent__ParseUserFromResponse();
        $mockOpenid->initialize($this->controller);


        // Merge the SReg response with the AX one and add the openid
        $mockOpenid->setReturnValue('__parseSRegResponse', array('foo' => 'bar', 'sreg' => 'sreg'));
        $mockOpenid->setReturnValue('__parseAXResponse', array('foo' => 'baz', 'ax' => 'ax'));
        $response = new OpenIdTest();
        $response->identity_url = 'http://';
        $expected = array('openid' => 'http://', 'foo' => 'baz', 'sreg' => 'sreg', 'ax' => 'ax');
        $result = $mockOpenid->__parseUserFromResponse($response);
        $this->assertEqual($result, $expected);

    }

    /**
     *  test__getSRegResponse
     *
     *  TODO : How to test those static methods ? Just hop the PEAR package was correctly tested
     **/
    function test__getSRegResponse() {

    }

    /**
     *  test__getAXResponse
     *
     *  TODO : How to test those static methods ? Just hop the PEAR package was correctly tested
     **/
    function test__getAXResponse() {

    }

	/**
	 *	test__parseSRegResponse
	 *
	 **/
	function test__parseSRegResponse() {
        // Set the gender if either M or F, null otherwise. Accepts lowercase
        $sreg = array('gender' => 'm');
        $result = $this->Openid->__parseSRegResponse($sreg);
        $this->assertNotNull($result['gender']);
        $sreg = array('gender' => 'F');
        $result = $this->Openid->__parseSRegResponse($sreg);
        $this->assertNotNull($result['gender']);
        $sreg = array('gender' => 'x');
        $result = $this->Openid->__parseSRegResponse($sreg);
        $this->assertNull($result['gender']);

        // Gets the nickname if such is set, otherwise use the fullname
        $sreg = array('nickname' => 'foo', 'fullname' => 'bar');
        $result = $this->Openid->__parseSRegResponse($sreg);
        $this->assertEqual($result['nickname'], 'foo');
        $sreg = array('fullname' => 'bar');
        $result = $this->Openid->__parseSRegResponse($sreg);
        $this->assertEqual($result['nickname'], 'bar');

        // Sets the name as the email
        $sreg = array('email' => 'foo');
        $result = $this->Openid->__parseSRegResponse($sreg);
        $this->assertEqual($result['name'], 'foo');

	}

    /**
	 *	test__parseAXResponse
	 **/
	function test__parseAXResponse() {
        // Fill only keys mapped and if value is not empty
        $ax = array(
            'http://axschema.org/namePerson/friendly' => array('foo'),
			'http://axschema.org/contact/email' => null,
			'http://axschema.org/nonExistent' => array('bar'),
        );
        $result = $this->Openid->__parseAXResponse($ax);
        $expected = array(
            'nickname' => 'foo'
        );
        $this->assertEqual($result, $expected);

	}




















}
