<?php
/**
 *	Document default settings
 **/
CaracoleConfigure::write(array(
	/**
	 *	CSS and JS files
	 **/
	'Packer' => array(
		'cssAdmin' => array(
			'CaracoleDocuments.form',
			'CaracoleDocuments.admin/style',
		),
		'jsAdmin' => array(
			'CaracoleDocuments.vendors/swfupload',			// Main SWFUpload javascript file
			'CaracoleDocuments.vendors/jquery-swfupload',	// jQuery plugin to bind events to SWFUpload
			'CaracoleDocuments.jquery-formDocument',		// jQuery plugin to upload files from a form
			'CaracoleDocuments.i18n/jquery-formDocument',	// Plugin translations
			'CaracoleDocuments.admin/init-document'			// Init the plugin

		)
	),

	/**
	 *	Autoload libs
	 **/
	'Autoload' => array(
		'CaracoleImage'		=> CARACOLE.'plugins'.DS.'caracole_documents'.DS.'libs'.DS.'caracole_image.php',
	),

	/**
	 *	I18n
	 **/
	'I18n' => array(
		'Image' => array(
			'human' 		=> __d('caracole_documents','Image', true),
			'plural' 		=> __d('caracole_documents','Images', true),
			'add'			=> __d('caracole_documents','Nouvelle image', true),
			'added'			=> __d('caracole_documents','Image "%1$s" ajoutée.', true),
			'edit'			=> __d('caracole_documents','Modifier image', true),
			'edited'		=> __d('caracole_documents','Image "%1$s" modifiée.', true),
			'deleted'		=> __d('caracole_documents','Images supprimées.', true),
			'restored'		=> __d('caracole_documents','Images restaurées.', true),
			'destroyed'		=> __d('caracole_documents','Images supprimées définitivement.', true),
			'reordered'		=> __d('caracole_documents','Images réorganisées.', true),
		)
	)
));
