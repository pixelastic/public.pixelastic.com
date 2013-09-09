<?php
/**
 *	PackerCssHelper
 *	This class extends the PackerHelper and deals with CSS files
 *
 *	Notice : We wrapped print rules inside @media print instead of using a <style media="print"> to save on HTTP
 *	requests. We have commented out the parts relative to cssPrint as they are deprecated. We should remove them in later versions
 *
 *	TODO : Parse CSS files with LESS : http://leafo.net/lessphp/
 **/
class PackerCssHelper extends PackerHelper {
	// Helpers
	var $helpers = array('Html', 'Javascript', 'Caracole.Fastcode');

	// Reference to the compressor
	var $compressor = null;

	// Css files to add to the display
	var $files = array();

	// Default options
	var $defaultOptions = array(
		'debug' => false,			//	Only if debug > 0
		'promote' => false,			//	Set to true to add before any other script
		'direct' => false,			//	Set to true to simply add the file without compressing it
		'media' => 'all'
	);

	function __construct() {
		parent::__construct();

		//	Where are the app css files placed ?
		$this->appDir = CSS;
		// Where are the css files places inside a plugin directory ?
		$this->pluginDir = 'webroot'.DS.'css'.DS;
		// Where are the compressed files saved ?
		$this->packedDir = CSS.'packed'.DS;
		// Path to access packed files
		$this->webDir = 'css/packed_';
		// What is the file extension
		$this->ext = 'css';

	}

	/**
	 *	addConfigureFiles
	 *	Setting default values
	 **/
	function addConfigureFiles($type = null, $promote = false) {
		// Getting the file list
		$default = empty($type) ? Configure::read('Packer') : Configure::read('Packer.'.$type);

		// Default admin files
		$this->add($default['cssAdmin'], array('admin' => true, 'promote' => $promote));

		// Debug files in debug mode
		$this->add($default['cssDebug'], array('debug' => true, 'promote' => $promote));

		//	Adding common CSS files
		$this->add($default['cssDefault'], array('promote' => $promote));
	}


	/**
	 *	compress
	 *	Compress a CSS text using CSSTidy
	 **/
	function compress($content) {
		if (empty($this->compressor))  {
			App::import('Vendor', 'CaracolePacker.csstidy/csstidy');
			$this->compressor = new csstidy();
			$this->compressor->load_template('high_compression');
		}
		//debug($content, true);
		//debug($this->compressor->settings);
		$this->compressor->parse($content);
		//debug($this->compressor->print->plain(), true);
		return $this->compressor->print->plain();
	}

	/**
	 *	tag
	 *	Return the <link> tag of the css
	 *
	 *	@param	string	$url	Url of the file
	 *	@paral	array	options	Options to pass to the Html->css call
	 **/
	function tag($url, $options = array()) {
		// Html tag
		return $this->Html->css($url, null, $options);
	}





}
