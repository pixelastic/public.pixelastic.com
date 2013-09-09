<?php
/**
 *	Antispam default settings
 **/
CaracoleConfigure::write(array(
	/**
	 *	CSS and JS files
	 **/
	'Packer' => array(
		'jsDefault' => array(
			'CaracoleAntispam.init',		// Using Caracole Ajax events
		)
	),
	/**
	 *	Antispam settings
	 **/
	'Antispam' => array(
		'emailField' => 'calirhoe',		// Default name of the real field holding the email value
	)
));
