<?php
/**
 *	ErrorsController
 *	This controller is mostly used to create a fixed 404 error page. We can then use it to redirect other pages to it
 **/
class CaracoleErrorsController extends AppController {

	/**
	 *	404
	 *	Display a 404 error page
	 **/
	function error404() {
		return $this->cakeError('error404');
	}

}