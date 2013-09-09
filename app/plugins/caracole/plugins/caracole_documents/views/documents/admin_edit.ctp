<?php

	/**
	 *	This file is a hook on the default admin_edit.ctp file.
	 *	We will display a preview of the current file
	 *
	 *	TODO : Maybe allowing editing the associated metadata. We have to take care while developping this feature that the metadata
	 *	are exact (ie. not setting a filesize not matching the file).
	 *	TODO : Maybe allowing for reuploading a new file to replace the old one. Updating all metadata but keeping the same id.
	 **/

	// Updating the filesize display to a more readable format
	$filesize = CaracoleNumber::toHumanSize($this->data['Document']['filesize']);
	$this->Form->data['Document']['filesize'] = $filesize;

	// Hiding the submit button
	$this->set('hideSubmit', true);

	// Default view
	echo $this->element(
		'..'.DS.'admin'.DS.'admin_edit',
		array('plugin' => 'caracole')
	);
