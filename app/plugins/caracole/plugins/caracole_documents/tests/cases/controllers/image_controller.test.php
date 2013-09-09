<?php
/**
 *	Testing the images_controller methods. Mostly the whole process() method
 **/
// Importing the base controllers and models
App::import('Controller', 'CaracoleDocuments.Documents');
App::import('Controller', 'CaracoleDocuments.Images');
App::import('Model', 'CaracoleDocuments.Image');

// Mocking the Auth to bypass the Authentication
App::import('Component', 'CaracoleUsers.CaracoleAuth');
Mock::generate('CaracoleAuthComponent', 'MockCaracoleAuthComponent');


/**
 *	ImageTestsController
 *	Extending the base controller to catch some methods
 **/
class ImageTestsController extends ImagesController {
    var $name = 'Images';

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

/**
 *	ImagesControllerTestCase
 *	Testing the controller
 **/
class ImagesControllerTestCase extends CakeTestCase {
    // Fixtures needed
	var $fixtures = array(
		'plugin.caracole_documents.image',
		'plugin.caracole_documents.image_metadata'
	);

	// Test directory
	var $testWebrootDirectory = '__TESTS__';

	/**
	 *	start
	 *	We will copy all the source files in our tests/source directory into the webroot so our methods
	 *	will correctly find them
	 **/
	function start() {
		parent::start();
		// We create a __TESTS__ directory in the root
		if (!file_exists($this->testWebrootDirectory)) {
			$tmpUmask = umask(0); // Little trick to correctly set the chmod
			mkdir($this->testWebrootDirectory, 0777);
			umask($tmpUmask);

		}
	}

	/**
	 *	end
	 *	When all the tests are finished, we remove the files from the webroot directory
	 **/
	 function end() {
		// Removing the test dierctory
		if (file_exists($this->testWebrootDirectory)) {
			$dir = opendir($this->testWebrootDirectory);
			// Deleting inner files
			while($file = readdir($dir)) {
				if ($file=='.' || $file=='..') continue;
				unlink($this->testWebrootDirectory.DS.$file);
			}
			// Closing and deleting the directory
			closedir($dir);
			rmdir($this->testWebrootDirectory);
		}
	}


    /**
	 *	startTest
	 *	Before every test.
	 *	We init the controller and disable the auth
	 *	We add in a shortcut access to the Images
	 *	We copy the sources images in the __TESTS__ directory
	 **/
	function startTest() {
		// Mock auth
		$this->mockAuth = new MockCaracoleAuthComponent();
		$this->mockAuth->enabled = false;

		// Loading the controller
		$this->controller = new ImageTestsController();
		$this->controller->constructClasses();

		// Disabling auth
		$this->controller->CaracoleAuth = $this->mockAuth;
		$this->controller->Component->initialize($this->controller);
		$this->controller->beforeFilter();
		$this->controller->Component->startup($this->controller);

		// Loading the sources
		$this->source1 = $this->controller->model->find('first', array('conditions' => array('Image.id' => 'source1')));

		// We copy the sources images in the webroot
		$itemList = $this->controller->model->find('all');
		$this->sourceDir = APP.'plugins'.DS.'caracole'.DS.'plugins'.DS.'caracole_documents'.DS.'tests'.DS.'sources'.DS;
		foreach($itemList as &$item) {
			if (!file_exists($item['Image']['path'])) copy($this->sourceDir.$item['Image']['id'].'.'.$item['Image']['ext'], $item['Image']['path']);
		}
    }

	/**
	 *	clean
	 *	Will cleans all the newly added images
	 **/
	function clean() {
		// Getting all versions and deleting them
		$this->controller->model->deleteAll(array('Image.id' => 'source1'), true, true);

	}

	/**
	 *	Convenient method to find a source from its id
	 **/
	function findSource($sourceId) {
		return  $this->controller->model->find('first', array(
			'conditions' => array('Image.id' => $sourceId),
			'contain' => array('Metadata', 'Version' => array('Metadata'))
		));

	}





	// Resizing to same dimensions return original file
	function testProcessToSameDimensions() {
		$result = $this->controller->__resize($this->source1, array('width' => 800, 'height' => 600));
		$this->assertEqual($result, $this->source1);
	}

	// Creating a new resize create a new entry in the db
	function testProcessCreateNewResize() {
		$nbrVersionBefore = $this->controller->model->Version->find('count', array('conditions' => array('Version.parent_id' => 'source1')));
		$result = $this->controller->__resize($this->source1, array('width' => 300, 'height' => 300, 'resize' => 'forced'));
		$nbrVersionAfter = $this->controller->model->Version->find('count', array('conditions' => array('Version.parent_id' => 'source1')));
		$this->assertEqual(1, $nbrVersionAfter - $nbrVersionBefore);

		$this->clean();
	}

	// Resizing to an already generated version return that version
	function testProcessAlreadyProcessedImage() {
		$existing = $this->controller->model->Version->find('first', array('conditions' => array('Version.id' => 'version1')));

		$result = $this->controller->__resize($this->source1, array('width' => 150, 'height' => 150));
		$this->assertEqual($result, $existing);
	}





}
