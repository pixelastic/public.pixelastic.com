<?php
App::import('Component', array('Caracole.Caracole', 'RequestHandler'));
App::import('Controller', 'AppController');
App::import('Model', 'AppModel');

// Mock Auth
App::import('Component', 'CaracoleUsers.CaracoleAuth');
Mock::generate('CaracoleAuthComponent', 'MockCaracoleAuthComponent');

// Mock RequestHandler
Mock::generate('RequestHandlerComponent', 'MockRequestHandlerComponent');


// Test controller
class CaracoleTestsController extends AppController {
	function render($action = null, $layout = null, $file = null) {
        $this->renderedAction = $action;
    }
	function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }
    function _stop($status = 0) {
        $this->stopped = $status;
    }
	function cakeError($error) {
		$this->error = $error;
	}
}
// Test model
class CaracoleTest extends AppModel {
	var $useTable = false;
	var $adminSettings = array(
		'views' => array('custom'),
		'toolbar' => array('main' => array('view' => 'mainToolbar'), 'secondary' => array('view' => 'secondaryToolbar'))
	);
}

class CaracoleTestCase extends CakeTestCase {

	function startTest() {
		// Mock auth
		$this->mockAuth = new MockCaracoleAuthComponent();
		$this->mockAuth->enabled = false;
		$this->mockAuth->urlLogout = 'urlLogout';
		// Mock RequestHandler
		$this->mockRequestHandler = new MockRequestHandlerComponent();

		// Controller
		$this->controller = new CaracoleTestsController();
		$this->controller->constructClasses();
		// Disabling auth
		$this->controller->CaracoleAuth = $this->mockAuth;

		// Setting default params
		$this->controller->params = array(
			'controller' => 'caracole_tests',
			'action' => 'view'
		);

		// Caracole component
		$this->component = new CaracoleComponent();
		$this->component->initialize($this->controller);

		// Binding the component to the controller
		$this->controller->Caracole = $this->component;

		// Here our controller is not fully initialized, but it not an issue because we are testing the component
	}

	/**
	 *	Initialize the whole controller by creating its component,
	 *	initializing them, calling its beforeFiler and calling
	 *	the startup method of components
	function initController() {
		$this->controller->Component->initialize($this->controller);
		$this->controller->beforeFilter();
		$this->controller->Component->startup($this->controller);
	}
	 */

	// We create a reference to the model in each controller
	function testSetModelReferenceInEachController() {
		$this->component->initialize($this->controller);

		$result = $this->controller->model;
		$this->assertEqual($result->name, 'CaracoleTest');
	}



	// DO not fix any flash session if the request is not flash
	function testDoNotFixFlashSessionIfNotFlashRequest() {
		$this->controller->RequestHandler = $this->mockRequestHandler;
		$this->mockRequestHandler->setReturnValue('isFlash', false);

		$result = $this->controller->Caracole->__fixFlashSession();
		$this->assertFalse($result);
    }

	// Do not fix if no sessionId and userAgent aren't passed in the form value
	function testNeedSessionIdAndUserAgentInFormToFixFlashSession() {
		$this->controller->RequestHandler = $this->mockRequestHandler;
		$this->mockRequestHandler->setReturnValue('isFlash', true);

		$this->controller->params = array();
		$result = $this->controller->Caracole->__fixFlashSession();
		$this->assertFalse($result);

		$this->controller->params = array(
			'form' => array(
				'sessionId' => 'foo',
				'userAgent' => null
			)
		);
		$result = $this->controller->Caracole->__fixFlashSession();
		$this->assertFalse($result);

		$this->controller->params = array(
			'form' => array(
				'sessionId' => null,
				'userAgent' => 'foo'
			)
		);
		$result = $this->controller->Caracole->__fixFlashSession();
		$this->assertFalse($result);
	}

    // Destroying the current session when fixing it
    function testFixingFlashSessionWillDestroyCurrentSession() {
        $this->controller->RequestHandler = $this->mockRequestHandler;
        $this->mockRequestHandler->setReturnValue('isFlash', true);

        $this->controller->params = array('form' => array('userAgent' => 'foo', 'sessionId' => 'bar'));
		$this->controller->Caracole->__fixFlashSession();
        $result = $this->controller->Session->id();
        $this->assertEqual('bar', $result);
    }

	// Keeping the actual layout if an error occured
	function testKeepCurrentLayoutIfError() {
		$this->controller->name = 'CakeError';

		$this->component->__setLayout();
		$result = $this->controller->layout;
		$this->assertEqual($result, 'default');
	}

	// Using the prefix layout if the action is prefixed
	function testSetPrefixLayout() {
		$this->controller->params['prefix'] = 'member';

		$this->component->__setLayout();
		$result = $this->controller->layout;
		$this->assertEqual($result, 'member');
	}

	// Keep the defined layout even if a prefix is set
	function testSetPrefixLayoutOnlyForDefaultLayout() {
		$this->controller->layout = 'custom';
		$this->controller->params['prefix'] = 'member';

		$this->component->__setLayout();
		$result = $this->controller->layout;
		$this->assertEqual($result, 'custom');
	}

	// In admin layout, we pass toolbars and logout url to the view
	function testSetAdminVarsToView() {
		$this->controller->action = 'admin_view';
		$this->controller->params['prefix'] = 'admin';

		$this->component->__setLayout();
		$result = $this->controller->viewVars;
		$this->assertEqual($result['urlLogout'], 'urlLogout');
		$this->assertEqual($result['mainToolbar'], 'mainToolbar');
		$this->assertEqual($result['secondaryToolbar'], 'secondaryToolbar');
	}

	// Using an Ajax layout for requests made by Ajax
	function testUseAjaxLayoutForAjaxRequests() {
		$this->controller->RequestHandler = $this->mockRequestHandler;
        $this->mockRequestHandler->setReturnValue('isAjax', true);

		$this->component->__setLayout();
		$result = $this->controller->layout;
		$this->assertEqual($result, 'ajax');

	}

	// But not if a custom parsed extension is set
	function testKeepCustomParsedExtensionLayoutEvenIfAjax() {
		$this->controller->RequestHandler = $this->mockRequestHandler;
        $this->mockRequestHandler->setReturnValue('isAjax', true);
		$this->controller->params['url'] = array('ext' => 'json');

		$this->component->__setLayout();
		$result = $this->controller->layout;
		$this->assertEqual($result, 'default');
	}

	// In admin, we search views inside the admin/ directory instead of the controller
	function testUseDefaultAdminViewsInAdminPanel() {
		$this->controller->layout = 'admin';
		$this->component->__setLayout();
		$result = $this->controller->viewPath;
		$this->assertEqual($result, 'admin');

	}

	function testUseDefaultAdminViewsInAdminPanelUnlessCustomViewDefined() {
		$this->controller->layout = 'admin';
		$this->controller->action = 'admin_custom';
		$this->component->__setLayout();
		$result = $this->controller->viewPath;
		$this->assertEqual($result, 'caracole_tests');

	}



}
