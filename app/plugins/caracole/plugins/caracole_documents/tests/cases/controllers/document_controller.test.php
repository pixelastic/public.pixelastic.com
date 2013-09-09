<?php
/**
 *	Testing this damn admin_upload method is quite hard. We need to emulate a flash request, then deal with newly
 *	instanciated models and pass params to emulate methods, save return values in Configure...
 *
 *	I just dropped this test case after two tests, it is really to hard to test
 *	It needs a rewrite of main class first, but also testing controller is a PITA, really...
 **/
App::import('Controller', 'CaracoleDocuments.Documents');

// Mock components
App::import('Component', array('RequestHandler', 'Caracole.Caracole', 'CaracoleUsers.CaracoleAuth'));
App::import('Model', 'CaracoleDocuments.Document');
Mock::generatePartial('CaracoleComponent', 'MockCaracoleNoFlashFix', array('__fixFlashSession'));
Mock::generatePartial('RequestHandlerComponent', 'MockRequestHandlerFlash', array('isFlash'));
Mock::generate('CaracoleAuthComponent', 'MockCaracoleAuthComponent');

// Dummy Page class
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

// Overriding the DocumentController
class DocumentsTestController extends DocumentsController {
    var $name = 'Documents';
	/**
	 *	beforeFilter
	 *	As doing a testAction will initiate a whole new instance, we need to define a custom
	 *	beforeFilter that will look at some special data fields and take appropriate actions
	 **/
	function beforeFilter() {
		// Mocking components
		$this->RequestHandler = new MockRequestHandlerFlash(); // Switching flash on/off
		$this->Caracole = new MockCaracoleNoFlashFix();	// Do not fix flash session
		$this->CaracoleAuth = new MockCaracoleAuthComponent();	// Do not ask for auth

		// Can switch flash on/off
		if (!empty($this->data['isFlash'])) {
			$this->RequestHandler->setReturnValue('isFlash', $this->data['isFlash']);
		}

		// Define if a file is uploaded or not
		if (!empty($this->data['uploadFile'])) {
			$this->params['form'] = array(
				'actionUrl' => '/admin/test_pages/edit/1',
				'fieldName' => 'data[TestPage][document_file]',
				'Filedata' => array('name' => 'bar')
			);
		}

		// Define what the insert method should return
		if (!empty($this->data['insert'])) {
			$this->model->setReturnValue('insert', $this->data['insert']);
		}
	}

	/**
	 *	beforeRender
	 *	We specifically set the view and layout vars because it seems that testAction does not
	 *	play nice is Router::parseExtension and our __setLayout method
	 **/
	function beforeRender() {
		App::build(array(
			'views' => CARACOLE.'plugins'.DS.'caracole_documents'.DS.'views'.DS
		));
		$this->layoutPath = $this->params['url']['ext'];
		$this->layout = empty($this->params['admin']) ? 'default' : 'admin';
		$this->viewPath = 'documents/'.$this->params['url']['ext'];
	}
	/*
	 function render($action) {
		debug($action);
	}
	*/
    function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }
    function _stop($status = 0) {
        $this->stopped = $status;
    }
	function cakeError($error) {
		// We save data in configure class because testAction dispatch to a new instance
		Configure::write('DocumentsTest.error', $error);
		$this->error = $error;
	}
}




class DocumentsControllerTestCase extends CakeTestCase {
    var $fixtures = array(
		'plugin.caracole_users.user', 'plugin.caracole_users.user_login_token', 'plugin.caracole_users.user_pass_token',
		'plugin.caracole_documents.document',
		'plugin.caracole_documents.metadata',
		'plugin.caracole_documents.document_page'
	);


    function startTest() {
		Configure::delete('DocumentsTest');
    }

	// Uploading a file not from flash is forbidden
	function testUploadCanOnlyBeDoneFromFlash() {
		$this->testAction('/admin/documents_test/upload.json', array('return' => 'result'));
		$this->assertEqual(Configure::read('DocumentsTest.error'), 'error404');
	}

	// Needed actionUrl and fieldName
	function testUploadNeedActionUrlAndFieldName() {
		$result = $this->testAction('/admin/documents_test/upload.json', array('return' => 'view', 'data' => array('isFlash' => true)));
		$this->assertEqual(Configure::read('DocumentsTest.error'), 'error404');
	}



}
