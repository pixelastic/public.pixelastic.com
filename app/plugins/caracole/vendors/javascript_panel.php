<?php
/**
 *	Javascript panel
 *	Adds a placeholder to display Javascript debug information
 **/
class JavascriptPanel extends DebugPanel {
	var $plugin = 'caracole';
	var $elementName = 'debug/javascript_panel';

	/**
	 *	__construct
	 *	Used to set a title using i18n
	 **/
	 function __construct() {
		$this->title = __d('caracole', 'Javascript', true);
		return parent::__construct();

	}
}