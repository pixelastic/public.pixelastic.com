<?php
/**
 *	Editor
 *	This model is not using any database, it only purpose is to serve as a back-end for any editor-relating AJAX calls
 **/
class Editor extends AppModel {
	//	Do not use table
	var $useTable = false;

	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
	// Admin settings
		$this->adminSettings = array(
			'views' => array('habtm', 'search'),
		);

		parent::__construct($id, $table, $ds);
	}
}
