<?php
// Upload url
Router::connect('/documents/upload/*',array('plugin' => 'caracole_documents', 'controller' => 'documents', 'action' => 'upload'));
Router::connect('/images/upload/*',array('plugin' => 'caracole_documents', 'controller' => 'images', 'action' => 'upload'));

// Image processing
Router::connect(
	'/images/:processData/:filename',
	array('plugin' => 'caracole_documents', 'controller' => 'images', 'action' => 'process'),
	array(
		'processData' => '[^/]+',
		'filename' => '[^/]+',
		'pass' => array('processData')
	)
);
Router::promote();
