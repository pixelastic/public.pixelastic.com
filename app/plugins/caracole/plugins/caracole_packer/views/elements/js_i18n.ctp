<?php
/**
 *	js_i18n
 *	Will load a php file containing i18n string and display a js file ready to load them into the $.i18n plugin
 *
 *	TODO : Might be a security risk to include php files like this. Any code would then be executed.
 *		I still need a way to write vars in a var and display them in another. (see Packer)
 **/

// Loading the filepath. Should contain a $jsI18n file
$jsI18n = array();
include($filepath);
if (empty($jsI18n)) return false;

// We add the array to the Javascript plugin
echo sprintf(
	'$(function() { $.i18n(\'%1$s.%2$s\', %3$s) });',
	Configure::read('Config.languageIso2'),
	$jsI18n['domain'],
	$this->Javascript->value($jsI18n['keys'])
);
?>
