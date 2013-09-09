<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php
 *
 * This is an application wide file to load any function that is not used within a class
 * define. You can also use this to include or require any files in your application.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
// Uncomment this line if you're having caching issues (eg. when adding a new plugin)
//Cache::clear();

// Edit this config values to reflect your app internationalization settings.
// This needs to be set here, because it is as early as we can afford
Configure::write('I18n', array(
	'default' => 'eng',					//	Default language
	'languages' => array('fre'),		//	Other available languages
	'labels' => array('fre' => 'Français', 'eng' => 'English'),
	'DefaultTimeZone' => 'Europe/Paris',//	Default timezone used to display dates. Don't forget to update the value in webroot/.htaccess too
));

// Loads all the required Caracole settings
require_once(APP.'plugins'.DS.'caracole'.DS.'config'.DS.'bootstrap.php');

// YOU CAN START ADDING YOUR OWN SETTINGS BELOW THIS LINE





?>