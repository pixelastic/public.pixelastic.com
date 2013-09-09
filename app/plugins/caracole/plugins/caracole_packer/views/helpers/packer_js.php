<?php
/**
 *	Helps in compressing Js files
 **/
class PackerJsHelper extends PackerHelper {
	// Helpers
	var $helpers = array('Html', 'Javascript', 'Caracole.Fastcode');

	// Css files to add to the display
	var $files = array();

	// Default options
	var $defaultOptions = array(
		'admin' => false,
		'debug' => false,
		'promote' => false,
		'direct' => false,
	);

	function __construct() {
		parent::__construct();

		//	Where are the app css files placed ?
		$this->appDir = JS;
		// Where are the js files places inside a plugin directory ?
		$this->pluginDir = 'webroot'.DS.'js'.DS;
		// Where are the compressed files saved ?
		$this->packedDir = JS.'packed'.DS;
		// Path to access packed files
		$this->webDir = 'js/packed_';
		// What is the file extension
		$this->ext = 'js';
	}

	/**
	 *	Adding files from the Configure options
	 **/
	function addConfigureFiles($type = null, $promote = false) {
		// Getting the file list
		$default = empty($type) ? Configure::read('Packer') : Configure::read('Packer.'.$type);

		// Js files for admin_ actions
		$this->add($default['jsAdmin'], array('admin' => true, 'promote' => $promote));

		// Js files for debug > 0
		$this->add($default['jsDebug'], array('debug' => true, 'promote' => $promote));

		// Mandatory javascript files
		$this->add($default['jsDefault'], array('promote' => $promote));

	}

	/**
	 *	Compress a Js text using JSMin
	 **/
	function compress($content) {
		App::import('Vendor', 'CaracolePacker.jsmin/jsmin');
		return JSMin::minify($content);
	}

	/**
	 *	Return the <script> tag of the js
	 *
	 *	@param	string	$url	Url of the file
	 **/
	function tag($url) {
		return $this->Javascript->link($url)."\n";
	}

}
