<?php
/**
 *	Contact default settings.
 *	This plugin could be used as-is, or be customised to fit a website special need.
 *	In this case you should :
 *		- Change the Contact.useModel and Contact.modelAlias values to someting specific like WebsiteContact
 *		- Create a WebsiteContact model and controller that should extend the Contact model and controller.
 *			They would extend the $validate and admin fields
 *		- Add new fields to the database
 *		- Create new views in app/views/website_contacts
 **/
CaracoleConfigure::write(array(
	/**
	 *	Contact settings
	 **/
	'Contact' => array(
		// Model to use
		'useModel' => 'CaracoleContact.Contact',
		'modelAlias' => 'Contact',
		'mail' => false, 	// Set to false if you don't want to send mails
		'bcc' => array(),	// Array list of BCC recipients
		'subject' => __d('caracole_contacts', '%1$s : You have received a contact request', true)
	)
));
