<?php
/**
 *	Work
 *	All my latest works
 **/
class Work extends AppModel {
	var $actsAs = array('Caracole.Draftable', 'Caracole.Sluggable');
	var $order = array('Work.date' => 'DESC');
	// belongsTo
	var $belongsTo = array(
		'Screen' => array(
			'className' => 'CaracoleDocuments.Image',
			'foreignKey' => 'image_screen'
		)
	);

	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		// Admin settings
		$this->adminSettings = array(
			'fields' => array(
				'name' => __('Name', true),
				'url' => __('Url', true),
				'text' => __('Text', true),
				'date' => __('Date', true),
				'image_screen' => __('Screen', true),
			)
		);

		//	Validation
		$this->validate = array(
			'name' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __("You have to write the name of the work", true)
				)
			)
		);

		parent::__construct($id, $table, $ds);
	}
}
