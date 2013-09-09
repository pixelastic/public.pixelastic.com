<?php
	/**
	 *	tinymce Packer
	 *	Will compress in one file all the files needed by tinyMCE
	 *	Based on tiny_mce_gzip.php
	 **/

	// We get the full file list by removing non-existing files
	$files = array();
	foreach($fileList as &$file) {
		$name = 'vendors'.DS.'tiny_mce'.DS.$file;
		$filepath = JS.$name.'.js';
		// Skipping non-existing files
		if (!file_exists($filepath)) continue;
		$files[] = array(
			'name' => $name,
			'filepath' => $filepath
		);
	}

	// We add a tinyMCE_GZ.start() after the core
	array_splice($files, 1, 0, array(array('content' => "tinyMCE_GZ.start();", 'name' => 'tinyMCE_GZ.start()')));

	// We get the url
	$url = $this->Packer->PackerJs->url($files);

	// We include it
	$filepath = JS.'packed'.DS.substr($url, strrpos($url, 'js/packed_')+10);
	include($filepath);

	// We disable the debug, otherwise, the debugKit parser will choke on a <head> statement in tinyMCE
	// and will add the whole page output inside our beloved JS file
	Configure::write('debug', 0);
