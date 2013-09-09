<?php
//	We redirect to an install page if Caracole is not installed
if (!Configure::read('Caracole.installed')) {
	// Install page
	Router::connect(
		'/*',
		array('plugin' => 'caracole_install', 'controller' => 'installs', 'action' => 'index')
	);
	return;
}

// Extensions to parse
Router::parseExtensions('json', 'rss');

//	We load all the config/routes.php files of each plugin
CaracoleConfigure::loadPluginFiles('routes');

// Admin routes
Router::connect('/admin/:controller/:action/*',array('admin' => true),array('routeClass' => 'CaracoleAdminRoute'));
Router::connect('/admin/:controller/:action/:id/*',array('admin' => true),array('routeClass' => 'CaracoleAdminRoute'));
Router::connect('/admin/:controller/*',array('admin' => true),array('routeClass' => 'CaracoleAdminRoute'));

Router::connect('/editors/:action', array('plugin' => 'caracole', 'controller' => 'editors'));
