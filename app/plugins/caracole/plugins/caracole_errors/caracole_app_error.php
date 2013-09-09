<?php
/**
 *	CaracoleAppError
 *	This class overwrite the default Error handler.
 *
 *	During development (debug=0), all errors will result be displayed in a very terse and readable layout (except for the 404 error), centering on
 *	the error.
 *	During production (debug>0), all errors will be displayed as 404 error but will be loggued in the database for easy access.
 *
 *	The default 404 error page will provide a custom search field (thanks to Google), but can be overwritten by dropping
 *	a new view in app/views/error
 **/
class CaracoleAppError extends ErrorHandler {


	/**
	 *	__construct
	 *	In prod, all errors are passed as error404 to _outputMessage
	 *	We need to overwrite the method to grab and save the error requested, as we may need it later
	 **/
	function __construct($method, $messages) {
		$this->errorName = $method;
		$this->errorParams = !empty($messages[0]) ? $messages[0] : null;
		return parent::__construct($method, $messages);
	}


	/**
	 *	error
	 *	Will set the default error displayed as a 404 error
	 **/
	function error($params) {
		$this->_outputMessage('error404');
	}


	/**
	 *	_outputMessage
	 *	Just an override of ErrorHandler method, we will always return a 404 header as well
	 *	as log the error in our database.
	 *
	 *	Also, in development mode, we will use a special error layout instead of the default layout.
	 **/
	function _outputMessage($template) {
		// Marking as 404 error
		$this->controller->header("HTTP/1.0 404 Not Found");

		// Allowing cake to grab views and layout from the plugin if not defined in the app
		App::build(array(
			'views' => array(
				VIEWS,
				CARACOLE.'plugins'.DS.'caracole_errors'.DS.'views'.DS
			)
		));

		// We log the error in prod
		if (Configure::read('debug')<=0) {
			$error = &ClassRegistry::init('CaracoleErrors.CaracoleError');
			$error->create(array(
				'url' => $this->controller->params['url']['url'],
				'name' => $this->errorName,
				'text'	=> $this->formatErrorParams(),
				'headers' => CaracoleRequest::getAllHeaders()
			));
			$error->save();
		} else {
			// We use a terse layout in dev, except for specific error404
			if ($template!='error404') {
				$this->controller->layout = 'error';
			}
		}

		parent::_outputMessage($template);
	}


	/**
	 *	formatErrorParams
	 *	Format in human readable form a list of error params
	 **/
	function formatErrorParams() {
		$ret = array();
		// Fast fail if no error params (like when directly going to /404)
		if (empty($this->errorParams)) return '';
		foreach($this->errorParams as $key => $value) {
			$ret[] = sprintf('%1$s : %2$s', $key, $value);
		}
		return implode("\r\n", $ret);
	}



}
