<?php
	/**
	 *	Login, logout and recover pass pages in the admin panel
	 **/
	// Login / Logout
	Router::connect('/admin/login', array('admin' => 1, 'controller' => 'users', 'plugin' => 'caracole_users', 'action' => 'login'));
	Router::connect('/admin/logout', array('admin' => 1, 'controller' => 'users', 'plugin' => 'caracole_users', 'action' => 'logout'));

	// Pass
	Router::connect('/admin/pass/*',array('admin' => 1, 'controller' => 'users', 'plugin' => 'caracole_users', 'action' => 'pass'));
	Router::connect('/admin/pass/:passToken',
		array('admin' => 1, 'controller' => 'users', 'plugin' => 'caracole_users', 'action' => 'pass'),
		array('pass' => array('passToken')));
