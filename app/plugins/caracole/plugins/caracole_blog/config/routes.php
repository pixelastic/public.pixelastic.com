<?php

// Post
App::import('Lib', 'CaracoleBlog.routes/CaracoleBlogPostRoute');
Router::connect(
	'/blog/:id::postSlug.html',
	array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'view'),
	array(
		'routeClass' => 'CaracoleBlogPostRoute',
		'postSlug' => '[^/]+',
		'pass' => array('id')
	)
);

// Search
Router::connect('/blog/search/:keyword/*',
	array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'search'),
	array(
		'pass' => array('keyword'),
		'keyword' => '[^/]+'
	)
);
Router::connect('/blog/search', array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'search'));

// Archive
Router::connect('/blog/archive/:year/:month/*',
	array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'archive'),
	array(
		'pass' => array('year', 'month'),
		'year' =>  '[12][0-9]{3}',
		'month' => '0[1-9]|1[012]'
	)
);
Router::connect('/blog/archive/:year/*',
	array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'archive'),
	array(
		'pass' => array('year', 'month'),
		'year' =>  '[12][0-9]{3}'
	)
);
Router::connect('/blog/archive/*', array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'archive'));

// Tags
App::import('Lib', 'CaracoleBlog.routes/CaracoleBlogTagRoute');
Router::connect(
	'/blog/tags/:tagSlug/*',
	array('plugin' => 'caracole_blog', 'controller' => 'tags', 'action' => 'view'),
	array(
		'pass' => array('tagSlug'),
		'routeClass' => 'CaracoleBlogTagRoute',
		'tagSlug' => '[^/]+',

	)
);
Router::connect('/blog/tags', array('plugin' => 'caracole_blog', 'controller' => 'tags', 'action' => 'index'));

// Comments
Router::connect('/blog/comments/:action/*', array('plugin' => 'caracole_blog', 'controller' => 'comments'));

// Blog index
Router::connect(
	'/blog/*',
	array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'index')
);