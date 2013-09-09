<?php
/**
 *	User default settings
 **/
CaracoleConfigure::write(array(
	/**
	 *	CSS and JS files
	 **/
	'Packer' => array(
		'jsAdmin' => array(
			'CaracoleUsers.admin/init-login'
		)
	),
	/**
	 *	Auth settings
	 **/
	'Auth' => array(
		'useModel' => 'CaracoleUsers.User',
		'modelAlias' => 'User',
		'selectOptions' => array()		//	Additional options to add when fetching the activeUser
	),
	/**
	 *	OpenId configuration
	 **/
	'OpenId' => array(
		/**
		 *	The list of providers to suggest to a user trying to login using openId
		 **/
		'providerList' => array(
			'Google' => array('url' => 'https://www.google.com/accounts/o8/id', 'icon' => 'google'),
			'Yahoo' => array('url' => 'https://me.yahoo.com/', 'icon' => 'yahoo'),
		)
	)
));
