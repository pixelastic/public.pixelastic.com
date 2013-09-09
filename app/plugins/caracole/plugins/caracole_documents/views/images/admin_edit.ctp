<?php

	/**
	 *	This file is a hook on the default admin_edit.ctp file.
	 *	We will display a preview of the current file
	 *
	 **/

	// Updating the filesize display to a more readable format
	$this->Form->data['Image']['filesize'] = CaracoleNumber::toHumanSize($this->data['Image']['filesize']);

	// Hiding the submit button
	$this->set('hideSubmit', true);

	// Default view
	echo $this->element(
		'..'.DS.'admin'.DS.'admin_edit',
		array('plugin' => 'caracole')
	);
