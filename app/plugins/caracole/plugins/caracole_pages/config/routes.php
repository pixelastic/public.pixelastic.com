<?php
// Home page
Router::connect(
	'/',
	array('plugin' => 'caracole_pages', 'controller' => 'pages', 'action' => 'view', 'pageSlug' => 'home'),
	array(
		'pass' => array('pageSlug')
	)
);

// Simple pages
// We won't use a custom route because we even accept non-existing slug
Router::connect(
	'/:pageSlug.html',
	array('plugin' => 'caracole_pages', 'controller' => 'pages', 'action' => 'view'),
	array(
		'pass' => array('pageSlug'),
		'pageSlug' => '[^/]+',

	)
);

