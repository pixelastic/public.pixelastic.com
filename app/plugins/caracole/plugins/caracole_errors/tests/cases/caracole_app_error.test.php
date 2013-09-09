 <?php
 /**
  *	CaracoleAppError
  *
  * Testing an ErrorHandler is not that easy. Whenever an error is supposed to occur when calling $this->cakeError(), then
  * an ErrorHandler is created, a new controller created, a view rendered and the script halts.
  *
  * So we need to :
  * 	- Create a TestErrorHandler that will extends the CaracoleAppError we want to test.
  * 		In that class, we will override the __construct() with a copy/paste of the parent, except that we will always
  * 		set the controller as an instance of CakeErrorController, because it extends AppController and we will need
  * 		to define special methods and properties for it.
  * 		We also save the errorName in an inner property, like we did in CaracoleAppError
  *
  * 		We will also overwrite the _stop() method to not call exit. We need to be able to call several Errors in a row
  *
  * 	- Create an AppController as seen above
  * 		- Will be extended by CakeErrorController, but we'll extends some of Controller's method
  * 			- We set the list of helpers that will be used in the error view
  * 			- We override the header() method or we'll end with 'Header already set' errors
  * 			- We override the render() method to keep track of the error rendered
  *
  * 	- Import the default Error and Controller classes.
  * 		- We need to import them after defining our AppController class. Otherwise cake will use its default AppController
  **/


// Loading core classes
App::import('Core', 'Error');


class AppController extends Controller {
	// Helpers used in the view. If not set, will generate a fatal error
	var $helpers = array('Caracole.Fastcode', 'CaracolePacker.Packer');
	// Overriding the header method. If not set, will generate 'Headers already set' errors;
	function header($header) {
		$this->header = $header;
	}
	function render($action) {
		$this->renderedAction = $action;
		return parent::render($action);
	}
}



// Testing error handler
class TestErrorHandler extends CaracoleAppError {
	// Copy/paste of ErrorHandler construct method, but force a new instance of CakeErrorController as $this->controller each time
	// CakeErrorController extends AppController, so we can overwrite its methods
	function __construct($method, $messages) {
		// Saving errorName in
		$this->errorName = $method;

		App::import('Core', 'Sanitize');

		$this->controller =& new CakeErrorController();
		$options = array('escape' => false);
		$messages = Sanitize::clean($messages, $options);

		if (!isset($messages[0])) {
			$messages = array($messages);
		}

		if (method_exists($this->controller, 'apperror')) {
			return $this->controller->appError($method, $messages);
		}

		if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($this)))) {
			$method = 'error';
		}
		if ($method !== 'error') {
			if (Configure::read('debug') == 0) {
				$parentClass = get_parent_class($this);
				if (strtolower($parentClass) != 'errorhandler') {
					$method = 'error404';
				}
				$parentMethods = array_map('strtolower', get_class_methods($parentClass));
				if (in_array(strtolower($method), $parentMethods)) {
					$method = 'error404';
				}
				if (isset($code) && $code == 500) {
					$method = 'error500';
				}
			}
		}
		$this->dispatchMethod($method, $messages);
		$this->_stop();
	}

	// Preventing the error from stopping all the request
	function _stop() {
		return;
	}
}






// Test case
class CaracoleAppErrorTest extends CakeTestCase {
	var $fixtures = array(
		'plugin.caracole_errors.caracole_error'
	);

	function startTest() {
		// Forcing debug to dev as a default
		Configure::write('debug', 2);
		// params to pass to errors
		$this->errorParams = array('className' => 'TestErrorController', 'message' => 'Dude !');

		$this->model = &ClassRegistry::init('CaracoleError');
	}

	// DEV : Error will use the error layout
	function testCallingErrorInDevWillUseErrorLayout() {
		ob_start();
		$errorHandler = new TestErrorHandler('missingController', $this->errorParams);
		$result = ob_get_clean();
		$this->assertEqual($errorHandler->controller->layout, 'error');
	}

	// DEV : 404 Error will use the default layout
	function testCalling404ErrorInDevWillUseDefaulLayout() {
		ob_start();
		$errorHandler = new TestErrorHandler('error404', $this->errorParams);
		$result = ob_get_clean();
		$this->assertEqual($errorHandler->controller->layout, 'default');
	}

	// Prod : Error will use the default layout
	function testCallingErrorInProdWillUseTheDefaultLayout() {
		Configure::write('debug', 0); //Production
		ob_start();
		$errorHandler = new TestErrorHandler('missingController', $this->errorParams);
		$result = ob_get_clean();
		$this->assertEqual($errorHandler->controller->layout, 'default');
	}

	// Prod : All errors are displayed as 404 errors
	function testCallingErrorInProdWillDisplay404Error() {
		Configure::write('debug', 0); //Production
		ob_start();
		$errorHandler = new TestErrorHandler(null, array());
		$result = ob_get_clean();
		$this->assertEqual($errorHandler->controller->renderedAction, 'error404');
	}

	// Prod : Errors are saved in the database
	function testErrorsSavedInDatabaseInProd() {
		Configure::write('debug', 0); //Production
		ob_start();
		$errorHandler = new TestErrorHandler('missingController', $this->errorParams);
		ob_clean();
		$result = $this->model->find('first');
		$this->assertEqual($result['CaracoleError']['type'], 'missingController');
	}



	// Error views defined in app/views/error are used if found
	function testErrorViewsDefinedInAppAreUsedInsteadOfDefault() {
		$sourceView = CARACOLE.'plugins'.DS.'caracole_errors'.DS.'tests'.DS.'files'.DS.'error404.ctp';
		$destinationFile = APP.'views'.DS.'errors'.DS.'error404.ctp';
		// Backing up existing file
		if (file_exists($destinationFile)) {
			$destinationFileBack = $destinationFile.'.bak';
			rename($destinationFile, $destinationFileBack);
		}
		// Copying file to app
		copy($sourceView, $destinationFile);

		ob_start();
		$errorHandler = new TestErrorHandler('error404', $this->errorParams);
		$result = ob_get_clean();
		$result = strpos($result, 'error404TestView');
		$this->assertTrue($result);

		// Deleting file
		unlink($destinationFile);
		// Restoring backed up file
		if (!empty($destinationFileBack) && file_exists($destinationFileBack)) {
			rename($destinationFileBack, $destinationFile);
		}

	}

}
































 /*
App::import('Core', 'Error');
// Dummy AppError to override _stop()
class AppError extends CaracoleAppError {
	/*
	 // Copied and pasted method from ErrorHandler. Updated to always acts as if there was no previous error
	function __construct($method, $messages) {
		App::import('Core', 'Sanitize');
		static $__previousError = null;

		$__previousError = array($method, $messages);
		$this->controller =& new CakeErrorController();
		$options = array('escape' => false);
		$messages = Sanitize::clean($messages, $options);

		if (!isset($messages[0])) {
			$messages = array($messages);
		}

		if (method_exists($this->controller, 'apperror')) {
			return $this->controller->appError($method, $messages);
		}

		if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($this)))) {
			$method = 'error';
		}
		if ($method !== 'error') {
			if (Configure::read('debug') == 0) {
				$parentClass = get_parent_class($this);
				if (strtolower($parentClass) != 'errorhandler') {
					$method = 'error404';
				}
				$parentMethods = array_map('strtolower', get_class_methods($parentClass));
				if (in_array(strtolower($method), $parentMethods)) {
					$method = 'error404';
				}
				if (isset($code) && $code == 500) {
					$method = 'error500';
				}
			}
		}
		$this->dispatchMethod($method, $messages);
		$this->_stop();
	}
	*


	// Overriding _stop
	function _stop() {
		// Saving the current layout
		Configure::write('TestError.layout', $this->controller->layout);
		// Preventing the request from exiting at the end of the error
		return;
	}





	function getPreviousError() {
		return self::$__previousError;
	}
}

// Dummy controller
class TestsController extends AppController {
    var $name = 'Test';
	var $useTable = null;
    var $autoRender = false;
    function render($action = null, $layout = null, $file = null) {
        $this->renderedAction = $action;
    }

	function cakeError($method, $messages = array()) {
		return new AppError($method, $messages);
	}
	function header($header) {
		echo $header;
	}

	// Convenient method to call an error and save its content
	function testFireError($errorName = 'error404') {
		ob_start();
		$this->cakeError($errorName);
		$this->errorContent = ob_get_clean();
		return;
	}

}









class CaracoleAppErrorTestCase extends CakeTestCase {

	// startTest
	function startTest() {
		ClassRegistry::flush();
		// Init the dummy controller that will fire errors
		$this->controller = ClassRegistry::init('TestsController');
		// Clearing the Configure class
		Configure::delete('TestError');
	}

	function endTest() {
	}



	// Calling an error in dev will use the error layout
	function testCallingErrorInDevWillUseErrorLayout() {
		$this->controller->testFireError('missingDatabase');
		$result = Configure::read('TestError.layout');
		$this->assertEqual($result, 'error');
	}

	// Calling an error in prod will use the default layout
	function testCallingErrorInProdWillUseTheDefaultLayout() {
		//$expected = $this->controller->layout;
		Configure::write('debug', 0);
		$this->controller->testFireError('missingDatabase');
		$result = Configure::read('TestError.layout');
		//$this->assertEqual($result, $expected);
	}

	// Calling a 404 error in dev will use the default layout

	// Error views defined in app/views/error are used if found
	*/


  ?>