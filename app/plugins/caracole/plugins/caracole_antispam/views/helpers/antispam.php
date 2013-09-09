<?php
/**
 *	Antispam
 *	Helper to prevent form spamming by adding cleverly crafted form fields to filter our spammers.
 *
 **/
class AntispamHelper extends AppHelper {
	// Helpers
	var $helpers = array('Caracole.Fastcode');


	/**
	 *	bait
	 *	Add an input field named email to lure spam bots into filling it. Will also add some other fields to gather stats on spams
	 *	The Antispam component should be enabled for this controller
	 **/
	function bait($options = array()) {
		// Spam bait field
		$options = Set::merge(array(
			'label' => __d('caracole_antispam', 'Spam bait', true),
			'help' => __d('caracole_antispam', 'Leave this field empty, it is only here to defeat spam bots', true),
			'div' => 'input text jsOff'
		), $options);
		return $this->Fastcode->input('email', $options);
	}

	/**
	 *	input
	 *	Displays a set of input fields used to filter out potential spammers
	 **/
	function input($options = array()) {
		// Default options
		$options = array(
			'bait' => array(),
			'javascript' => array(),
			'timestamp' => array()
		);

		// Constructing the inputs
		$return = '';
		foreach($options as $key => &$value) {
			// Skipping unset keys
			if (!isset($options['bait']) || $options['bait']===false) continue;

			$return.= $this->{$key}($value);
		}

		return $return;

	}

	/**
	 *	javascript
	 *	Adds an hidden field to check if the user has javascript enabled
	 **/
	function javascript($options = array()) {
		$options = Set::merge(array(
			'type' => 'hidden',
			'secure' => true,
			'secureValue' => false,
			'value' => 0
		), $options);
		return $this->Fastcode->input('spam_js', $options);
	}



	/**
	 *	timestamp
	 *	Adds an hidden timestamp field to calculate how fast the form is submitted
	 **/
	function timestamp($options = array()) {
		$options = Set::merge(array(
			'type' => 'hidden',
			'value' => mktime()
		), $options);
		return $this->Fastcode->input('spam_timestamp', $options);
	}




}
