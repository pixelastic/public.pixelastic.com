<?php
/**
 *	Caracole
 *	Every Caracole controller will use this component.
 *	It will give a shortcut ($this->model) to every controller to target its model
 *	And auto set the layout based on the prefix and type of request
 *
 *	It will also fix a damn bug involving Flash and cakePHP sessions
 **/
class CaracoleComponent extends Object {

	/**
	 *	initialize
	 *	Fired right before the controller beforeFilter method
	 **/
	function initialize(&$controller) {
		// We keep a reference to the controller in this component
		$this->controller = &$controller;
		// We create a shortcut in this controller to easily target its model
		$this->controller->model = &$this->controller->{$this->controller->modelClass};

		// Fixing the dreaded Flash session issue
		$this->__fixFlashSession();
	}

	/**
	 *	startup
	 *	Fired after the controller beforeFilter but before the controller action
	 *	Used to define view paths and layout
	 **/
	function startup(&$controller) {
		// We select the correct layout
		$this->__setLayout();
	}



	/**
	 *	__setLayout
	 *	Set the correct layout based on the actual prefix and the kind of request.
	 **/
	function __setLayout() {
		// Getting prefix
		$prefix = (!empty($this->controller->params['prefix'])) ? $this->controller->params['prefix'] : false;
		// Replacing default layout to admin layout
		if ($this->controller->layout=='default' && !empty($prefix)) {
			$this->controller->layout = $prefix;
		}

		// No more layout sniffing when an error occured
		if ($this->controller->name=='CakeError') {
			return;
		}

		// Passing additional variable to the admin layout
		if ($this->controller->layout=='admin') {
			// Is this action using a custom view ? If no, we use the views in the admin/ dir
			$simpleAction = str_replace('admin_', '', $this->controller->action);
			if (!in_array($simpleAction, $this->controller->model->adminSettings['views'])) {
				$this->controller->viewPath = str_replace(Inflector::tableize($this->controller->name), 'admin', $this->controller->viewPath);
			}

			// We pass toolbars to the view
			$mainToolbarArray = $this->controller->model->adminSettings['toolbar']['main'];
			$mainToolbar = empty($mainToolbarArray[$simpleAction]) ? array() : $mainToolbarArray[$simpleAction];
			$secondaryToolbarArray = $this->controller->model->adminSettings['toolbar']['secondary'];
			$secondaryToolbar = empty($secondaryToolbarArray[$simpleAction]) ? array() : $secondaryToolbarArray[$simpleAction];

			$this->controller->set(array(
				'mainToolbar'		=> $mainToolbar,
				'secondaryToolbar'	=> $secondaryToolbar,
				'urlLogout'			=> $this->controller->CaracoleAuth->urlLogout
			));
		}

		// We stop if the extension is parsed, because it means cake will already take care of layout change
		if (!empty($this->controller->params['url']['ext']) && $this->controller->params['url']['ext']!="html") {
			return;
		}

		// Using a bare layout for ajax calls
		if ($this->controller->RequestHandler->isAjax()) {
			$this->controller->layout = 'ajax';
		}
	}


	/**
	 *	__fixFlashSession
	 *	Flash represent itself as a different userAgent than your browser. It means that any request done through flash
	 *	will lost the user cookies and session values.
	 *
	 *	So we will allow Flash to send us the sessionId it needs to be using.
	 *
	 *	PHP do not allow us to change a session id once the session is started. That's why we must include the Caracole
	 *	component (and thus this method) first in the list of component. That way we can switch the sessionId before any
	 *	other script even need it.
	 *
	 *	I am never sure I'll don't have to come back on this method later because it sometime seems to broke with new versions.
	 *	There is a detailed explanation of the previous fixes I ran on this issue :
	 *		http://www.pixelastic.com/blog/208:swfupload-and-cakephp
	 *
	 *
	 **/
	function __fixFlashSession() {
		if (!$this->controller->RequestHandler->isFlash()) return false;
		if (empty($this->controller->params['form'])) return false;
		$data = $this->controller->params['form'];
		// We need both sessionId and userAgent
		if (empty($data['sessionId'])) return false;
		//if (empty($data['userAgent'])) return false;

		// Changing the sessionId
		$this->controller->Session->id($data['sessionId']);
		return;

		// Below are a number of previous fixes that helped fixed the issue. It seems that moving this component on top
		// of the stack is a better way to go.

		// Destroying the session created by flash
		//$this->controller->Session->destroy();

		//	Using instead the session specified
		//$this->controller->Session->id($data['sessionId']);

		//	We revert to the original userAgent because starting a new session modified it
		//$this->controller->Session->write('Config.userAgent', $data['userAgent']);

		// Starting the session
		//$this->controller->Session->start();

		//	We delete the flash cookie, forcing it to restart this whole process on each request
		//setcookie(Configure::read('Session.cookie'), '', time() - 42000, $this->controller->Session->path);

		// Sometimes starting the session, changing its id, or writing in it corrupted its content. I did not managed
		//to correctly track the issue...
	}

}
