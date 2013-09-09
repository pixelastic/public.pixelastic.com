<?php

// Default 404 error page. Could then be used by the .htaccess redirects
Router::connect('/404/*', array('plugin' => 'caracole_errors', 'controller' => 'caracole_errors', 'action' => 'error404'));

// Admin error page, rewriting with better url
Router::connect('/admin/errors/*', array('plugin' => 'caracole_errors', 'controller' => 'caracole_errors', 'admin' => true, 'action' => 'index'));