<?php
/**
 *	CaracoleAppHelper
 *
 *	This class works as a buffer between the AppHelper and the cake main Helper. It is used to override some core methods.
 **/
class CaracoleAppHelper extends Helper {

	/**
	 *	url
	 *	We override the main url() method to add our I18n logic. It will prepend the generated url with the current language
	 **/
	function url($url = null, $full = false) {
		// If the given url is a string, we won't add any more I18n logic to it and just return it as-is
		if (is_string($url)) return parent::url($url, $full);

		// If we are in the default language, we won't do anything
		if (Configure::read('I18n.default')==Configure::read('Config.language')) return parent::url($url, $full);

		// We get the default url
		$url = parent::url($url, $full);

		// Prepending the language
		if (empty($full)) {
			return '/'.Configure::read('Config.language').'/'.trim($url, '/');
		} else {
			return str_replace(FULL_BASE_URL, FULL_BASE_URL.'/'.Configure::read('Config.language'), $url);
		}
	}




}