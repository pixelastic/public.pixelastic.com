<?php
/**
 *	Caracole settings
 *	Checking if Caracole is installed. When Caracole first installed itfself, it creates a file named "installed"
 *	in the app/config/ directory.
 **/
Configure::write('Caracole.installed', file_exists(APP.'config'.DS.'installed'));

// While working from a shell, we assume production, unless 'dev' is passed as argument
if (defined('CAKEPHP_SHELL')) {
	// Based on the prod/dev flag
	$args = env('argv');
	$environment = 'prod';
	foreach($args as $flag) {
		if ($flag=='dev') {
			$environment = 'dev';
			break;
		}
	}
} else {
	// Based on the server url
	$environment = (env('SERVER_ADDR')=='127.0.0.1') ? 'dev' : 'prod';
}
Configure::write('Caracole.environment', $environment);

// Define Caracole base plugin directory
define('CARACOLE', APP.'plugins'.DS.'caracole'.DS);

/**
 *	We tell cake to find Caracole core plugins in the plugins/caracole/plugins/ directory
 **/
App::build(array(
	'plugins' 	=> array(CARACOLE.'plugins'.DS),
	//'views' 	=> array(CARACOLE.'views'.DS)
));

/**
 *	We define some path in the app where cakePHP (and PHP) is supposed to find some of the classes we are referencing
 *	We store them in Configure.Autoload and check for them in __autoload. If the key is defined, we load
 *
 *	Some plugins special classes are defined here we absolutly need them loaded, even before the config files get read
 **/
Configure::write('Autoload', array(
	'CaracoleAdminRoute'		=> CARACOLE.'libs'.DS.'routes'.DS.'caracole_admin_route.php',
	'CaracoleAppModel' 			=> CARACOLE.'caracole_app_model.php',
	'CaracoleAppController' 	=> CARACOLE.'caracole_app_controller.php',
	'CaracoleAppHelper' 		=> CARACOLE.'caracole_app_helper.php',
	'CaracoleAppError' 			=> CARACOLE.'plugins'.DS.'caracole_errors'.DS.'caracole_app_error.php',
	'CaracoleAppView'	 		=> CARACOLE.'caracole_app_view.php',

	'CaracoleCache'				=> CARACOLE.'libs'.DS.'caracole_cache.php',
	'CaracoleConfigure'			=> CARACOLE.'libs'.DS.'caracole_configure.php',
	'CaracoleInflector'			=> CARACOLE.'libs'.DS.'caracole_inflector.php',
	'CaracoleI18n'				=> CARACOLE.'plugins'.DS.'caracole_i18n'.DS.'libs'.DS.'caracole_i18n.php',
	'CaracoleNumber'			=> CARACOLE.'libs'.DS.'caracole_number.php',
	'CaracoleRequest'			=> CARACOLE.'libs'.DS.'caracole_request.php',
	'CaracoleSecurity'			=> CARACOLE.'libs'.DS.'caracole_security.php',
));
// Autoload method
function __autoload($className) {
	// If the class exists, we can stop, everything is fine
	if (class_exists($className, false)) return;

	// Maybe we have it in the defined list ?
	$autoloadPath = Configure::read('Autoload.'.$className);
	if (empty($autoloadPath)) return;

	// We include it
	require_once($autoloadPath);
}

// We set the default language based on the url
CaracoleI18n::init();

//	We init the cache engines used by Caracole
CaracoleCache::init();

//	We load the plugin specific settings
CaracoleConfigure::loadPluginFiles('config');

//	We load the app specific settings
require_once(APP.'config'.DS.'config.php');

//	We load the plugin translation files
CaracoleConfigure::loadPluginFiles('i18n');

// We add inflector rules
CaracoleInflector::init();

//	We fire the plugins bootstraps
//CaracoleConfigure::loadPluginFiles('bootstrap');
