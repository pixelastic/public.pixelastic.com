<?php
/**
 *	Page
 *	Helps in displaying classic pseudo-dynamic pages.
 **/
class Page extends AppModel {
	var $actsAs = array(
		'Caracole.Draftable',
		'Caracole.Sluggable'

	);
	var $order = array('Page.order' => 'ASC', 'Page.id' => 'ASC');

	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		// Admin settings
		$this->adminSettings = array(
			'fields' => array(
				'name' => array(
					'label' => __d('caracole_pages', 'Page name', true),
					'required' => true
				),
				'text' => __d('caracole_pages', 'Text', true)
			),
			'toolbar' => array(
				'secondary' => array(
					'index' => array('reorder')
				)
			)
		);

		//	Validation
		$this->validate = array(
			'name' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_pages', 'You must specify the name of the page', true)
				)
			),
			'slug' => array(
				'unique' => array(
					'rule' => 'isUnique',
					'message' => __d('caracole_pages', 'Another page is using the same slug', true)
				)
			),
		);

		parent::__construct($id, $table, $ds);

	}


}
