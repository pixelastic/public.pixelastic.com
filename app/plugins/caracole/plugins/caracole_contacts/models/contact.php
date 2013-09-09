<?php
/**
 *	Contact
 *
 **/
class Contact extends AppModel {
	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		// Getting alias. This class can be extended in app
		$this->alias = $id['alias'];
		// Admin settings
		$this->adminSettings = array(
			'index' => array(
				'headers' => array(
					$this->alias.'.name' => __d('caracole_contacts', 'Name', true),
					$this->alias.'.email' => __d('caracole_contacts', 'Mail', true),
				),
				'paginate' => array(
					$this->alias => array(
						'fields' => array(
							$this->alias.'.email'
						),
						'order' => array(
							$this->alias.'.created' => 'DESC'
						)
					)
				)
			),
			'toolbar' => array(
				'main' => array(
					'index' => array(
						'add' => false,
					),
				),
			),
			'fields' => array(
				'name' => array(
					'label' => __d('caracole_contacts', 'Name', true),
				),
				'email' => array(
					'label' => __d('caracole_contacts', 'Email', true),
				),
				'text' => array(
					'label' => __d('caracole_contacts', 'Message', true),
					'type' => 'textarea',
				),
			)
		);

		//	Validation
		$this->validate = array(
			'name' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_contacts', 'You have to type your name.', true)
				)
			),
			'email' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_contacts', "You have to type your email if you want me to contact you back.", true)
				),
				'mailValid' => array(
					'rule' => array('email', false),
					'message' => __d('caracole_contacts', 'This email does not seem to be valid.', true)
				)
			),
			'text' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_contacts', "Haven't you something to say ?", true)
				)
			)
		);

		parent::__construct($id, $table, $ds);
	}




}
