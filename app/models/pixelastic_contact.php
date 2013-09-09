<?php
/**
 *	PixelasticContact
 *	Overwrite the default Contacts to add new fields
 **/
App::import('Model', 'CaracoleContacts.Contact');
class PixelasticContact extends Contact {
	var $useTable = 'contacts';
	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);



		// Adds new fields
		$this->adminSettings['fields']+= array(
			'timeframe' => array(
				'label' => __('Timeframe', true),
			),
			'budget' => array(
				'label' => __('Budget', true),
			),
		);

	}
}
