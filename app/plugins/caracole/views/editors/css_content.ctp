<?php
	/**
	 *	tinyMCE editor css
	 *	Will compress in one file the main editor CSS rules + custom rules of the current website
	 **/

	$files = array(
		// Default editor file
		array('name' => 'Caracole.admin/editor', 'filepath'	=> CARACOLE.'webroot'.DS.'css'.DS.'admin'.DS.'editor.css'),
		// Custom editor file
		array('name' => 'admin/editor', 'filepath' => APP.'webroot'.DS.'css'.DS.'admin'.DS.'editor.css')
	);

	// We get the url
	$url = $this->Packer->PackerCss->url($files);

	// We include it
	$filepath = CSS.'packed'.DS.substr($url, strrpos($url, 'css/packed_')+11);
	include($filepath);

	// We disable the debug, otherwise, the debugKit parser will choke on a <head> statement in tinyMCE
	// and will add the whole page output inside our beloved JS file
	Configure::write('debug', 0);
