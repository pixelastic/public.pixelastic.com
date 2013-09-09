<?php
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

// Do not remove this line. It does load all the required Caracole routes
require_once(APP.'plugins'.DS.'caracole'.DS.'config'.DS.'routes.php');

// You can add your app-specific routes below this line. If you want to overwrite caracoles routes, you have to add Router::promote() after

// Index goes to blog
Router::connect('/', array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'index', 'ext' => 'html'));
Router::promote();

// Works
Router::connect('/work', array('plugin' => null, 'controller' => 'works', 'action' => 'index'));
Router::promote();

// Overwriting contact routes
Router::connect('/contact/*', array('controller' => 'pixelastic_contacts', 'action' => 'add'));
Router::promote();






/**
 *	URL SEO compatibility with previous site version
 **/
// Old realisation page
Router::connect(
	'/realisations/*',
	array('plugin' => 'caracole_pages', 'controller' => 'pages', 'action' => 'view', 'pageSlug' => 'work', 'ext' => 'html'),
	array('pass' => array('pageSlug'))
);
Router::promote();
// Old blog posts
Router::connect(
	'/blog/:id::postSlug',
	array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'view'),
	array(
		'routeClass' => 'CaracoleBlogPostRoute',
		'postSlug' => '[^/]+',
		'pass' => array('id')
	)
);
Router::promote();
// Old rss
Router::connect('/posts/rss', array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'index', 'url' => array('ext' => 'rss')));
Router::promote();


?>