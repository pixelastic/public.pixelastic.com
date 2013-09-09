<?php
/**
 *	Fastcode
 *	This helper is a wrapper for all the inner Caracole helpers. It is mostly a convenient helper, we won't have to
 *	remember in which helper the methods was defined.
 *	Thanks to some PHP5 goodness all calls will be redirected to the correct helper.
 **/
App::import('Sanitize');
class FastcodeHelper extends AppHelper {
	// Helpers used in this helper
	var $helpers = array(
		'Caracole.CaracoleHtml',		//	CARACOLE : Convenient methods for displaying html and text
		'Caracole.CaracoleForm',		//	CARACOLE : Convenient methods for displaying forms and inputs
		'CaracoleIcons.Icon',					//	CARACOLE : Methods for displaying icons
	);

	// We store the mapping collection of methods and their helper
	var $methodCollection = array();

	/**
	 *	beforeRender
	 *	We will save the list of inner helpers methods in this helper method collection
	 *	It will be used later to dispatch undefined method calls
	 **/
	function beforeRender() {
		// Stopping if the collection already populated
		if (!empty($this->methodCollection)) {
			return true;
		}

		// Saving methods in method collection
		foreach($this->helpers as &$helperName) {
			// Getting helper name for plugins
			if (strpos($helperName, '.')) {
				list($pluginName, $helperName) = explode('.', $helperName);
			}
			// Getting this helper methods (by removing its parent methods)
			$parentMethods = get_class_methods(get_parent_class($this->{$helperName}));
			$pluginMethods = array_diff(get_class_methods($this->{$helperName}), $parentMethods);
			// Saving them in the collection
			foreach($pluginMethods as $methodName) {
				if (substr($methodName, 0, 2)=='__') continue;
				$this->methodCollection[$methodName] = $helperName;
			}
		}

		return true;
	}

	/**
	 *	__call
	 *	PHP5 goodness called whenever trying to call an undefined method.
	 *	We will search for it in our method collection and dispatch to the correct helper if found
	 **/
	function __call($name, $arguments) {
		// Not found in our collection
		if (empty($this->methodCollection[$name])) {
			return parent::__call($name, $arguments);
		}
		// We call the method found in one of the helpers
		return call_user_func_array(array(&$this->{$this->methodCollection[$name]}, $name), $arguments);
	}

	/**
	 *	model
	 *	General method to return the name of the current model.
	 **/
	function model() {
		return $this->params['models'][0];
	}



}
