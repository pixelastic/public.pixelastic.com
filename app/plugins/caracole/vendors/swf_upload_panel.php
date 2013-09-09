<?php
/**
 *	SwfUpload panel
 *	Adds a placeholder to display SwfUpload debug information
 **/
class SwfUploadPanel extends DebugPanel {
	var $plugin = 'caracole';
	var $elementName = 'debug/swf_upload_panel';

	/**
	 *	__construct
	 *	Used to set a title using i18n
	 **/
	 function __construct() {
		$this->title = __d('caracole', 'SWFUpload', true);
		return parent::__construct();

	}
}