<?php
App::import('Controller', 'CaracolePages.Pages');

// Dummy Page class
/*
 class TestPage extends CakeTestModel {
	var $name = 'Page';
	//var $useTable = 'pages';
	var $belongsTo = array(
		'File' => array(
			'className' => 'CaracoleDocuments.Document',
			'foreignKey' => 'document_file'
		)
	);
	var $adminSettings = array();
}
*/

// Mock Auth
App::import('Component', 'CaracoleUsers.CaracoleAuth');
Mock::generate('CaracoleAuthComponent', 'MockCaracoleAuthComponent');

// Overriding the PagesController
class PageTestsController extends PagesController {
    var $name = 'Pages';
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

class PagesControllerTestCase extends CakeTestCase {
    var $fixtures = array(
		//'plugin.caracole_users.user', 'plugin.caracole_users.user_login_token', 'plugin.caracole_users.user_pass_token',
		'plugin.caracole_pages.page'
	);


    function startTest() {
		// Mock auth
		$this->mockAuth = new MockCaracoleAuthComponent();
		$this->mockAuth->enabled = false;

		$this->controller = new PageTestsController();
		$this->controller->constructClasses();
		// Disabling auth
		$this->controller->CaracoleAuth = $this->mockAuth;
		$this->controller->Component->initialize($this->controller);
		$this->controller->beforeFilter();
		$this->controller->Component->startup($this->controller);

		//$this->pluginViewPath = CARACOLE.'plugins'.DS.'caracole_pages'.DS.'tests'.DS.'views'.DS;
		//App::build(array('views' => array($this->pluginViewPath)));

    }

	// Can't display a page with an empty slug
	function testNoEmptySlug() {
		$this->controller->view();
		$result = $this->controller->error;
		$this->assertEqual($result, 'error404');
	}

	// Getting values from the page
	function testGetPageData() {
		$this->controller->view('about');
		$result = $this->controller->viewVars;
		$this->assertEqual($result['item']['Page']['text'], 'Lorem Ipsum');
	}

	// TODO : Find a way to test that the correct views gets displayed...

	/*
	 // Find a file named as the slug in the directory and use it as view
	function testFindCustomSlugView() {
		$result = $this->controller->__getViewFile('about');
		$this->assertEqual($result, $this->pluginViewPath.'pages'.DS.'about.ctp');
	}

	// Revert to view.ctp if no file found
	function testFindDefaultView() {
		$result = $this->controller->__getViewFile('blah');
		$this->assertEqual($result, $this->pluginViewPath.'pages'.DS.'view.ctp');
	}
	*/





}
