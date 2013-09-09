<?php
/**
 *	error404.ctp
 *	Default error 404 page. Will hopefully help the lost user found its way to the page he/she was looking for
 **/

	// Setting title and description
	$this->set(array(
		'pageCssClass' => 'error404',
		'title_for_layout' => __d('caracole_errors', '404 Error : Page not found', true)
	));

	// Sorry
	echo $this->Fastcode->message(
		__d('caracole_errors', 'Sorry, the page you are looking for does not exists.', true),
		'error'
	);

	// Maybe google can help ?
	echo $this->Javascript->codeBlock(sprintf(
		'var GOOG_FIXURL_LANG = "%1$s"; var GOOG_FIXURL_SITE = "%2$s";',
		Configure::read('Config.languageIso2'),
		Configure::read('SiteUrl.lang')
	));
	echo $this->Javascript->link('http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js');


	$script = "$(function() { $('#goog-wm-sb').addClass('button'); });";
	$this->Packer->js($script, array('content' => true, 'direct' => true));

	// Disabling the sql output
	Configure::write('debug', 0);
