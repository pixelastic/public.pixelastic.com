<?php
/**
 *	CaracoleError
  *	This model will be used to log errors in the database.
  *
  *	We can not name this Model 'Error' because it will conflict with cakePHP inner ErrorHandler class.
  *	We still use a table named errors, tho.
 **/
class CaracoleError extends AppModel {
	var $useTable = 'errors';
	var $order = array('CaracoleError.created' => 'DESC');

	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		// Admin settings
		$this->adminSettings = array(
			'toolbar' => array(
				'main' => null
			),
			'index' => array(
				'headers' => array(
					'CaracoleError.name' => __d('caracole_errors', 'Type', true),
					'CaracoleError.url' => __d('caracole_errors', 'Url', true)
				),
				'paginate' => array(
					'CaracoleError' => array(
						'fields' => array(
							'CaracoleError.url',
						)
					),
				)
			),
			'fields' => array(
				'name' => array(
					'label' => __d('caracole_errors', 'Type', true),
					'plain' => true
				),
				'url' => array(
					'label' => __d('caracole_errors', 'Url', true),
					'plain' => true
				),
				'text' => array(
					'label' => __d('caracole_errors', 'Text', true),
					'plain' => true
				),
				'headers' => array(
					'label' => __d('caracole_errors', 'Headers', true),
					'plain' => true,
					'type' => 'textarea',
					'advanced' => true
				)
			)
		);

		parent::__construct($id, $table, $ds);

	}

}