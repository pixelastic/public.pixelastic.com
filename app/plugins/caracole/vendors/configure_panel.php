<?php
/**
 *	Configure panel
 *	Adds a placeholder to display Configure debug information
 **/
class ConfigurePanel extends DebugPanel {
	var $plugin = 'caracolet';
	var $elementName = 'debug/configure_panel';

	/**
	 *	__construct
	 *	Used to set a title using i18n
	 **/
	 function __construct() {
		$this->title = __d('caracole', 'Configure', true);
		return parent::__construct();
	}

	/**
	 *	Passing variables to the view
	 **/
	function beforeRender(&$controller) {
		$out = array();

		// I18n configuration
		$out['i18n'] = array(
			'Config.language' 			=> Configure::read('Config.language'),
			'SiteUrl.lang' 				=> Configure::read('SiteUrl.lang'),
			'I18n.default' 				=> Configure::read('I18n.default'),
			'I18n.DefaultTimeZone'		=> Configure::read('I18n.DefaultTimeZone'),
			'Config.languageIso2' 		=> Configure::read('Config.languageIso2'),
		);

		return $out;
	}



}