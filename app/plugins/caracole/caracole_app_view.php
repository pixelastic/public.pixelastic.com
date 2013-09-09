<?php
/**
 *	CaracoleAppView
 *
 *	This class is used to overwrite the main view used by cakePHP.
 *	Some of the core method do not play nice with our Caracole implementation, so we short-circuit
 *	them here to tweak them
 **/
class CaracoleAppView extends View {
	/**
	 *	_paths
	 *	This method is supposed to return the list of all directories where cake should search when using views, helpers
	 *	and layouts.
	 *
	 *	We will override it to always search in the main app/views directory first. This will allow us to overwrite the plugin
	 *	views on a project-specific basis.
	 *	Then if we are dealing with a caracole plugin, we search in the plugin views/ directory
	 *	And finally, we search in the main caracole directory
	 **/
	function _paths($plugin = null, $cached = true) {
		// Main app/views
		$paths = array(VIEWS);
		// Plugin views/
		if (!empty($plugin)) {
			$paths[] = App::pluginPath($plugin).'views'.DS;
		}
		// Caracole views/
		$paths[] = CARACOLE.'views'.DS;

		// We prepend those path to the parent list
		$this->__paths = array_unique(array_merge($paths, parent::_paths($plugin, $cached)));
		return $this->__paths;
	}




}