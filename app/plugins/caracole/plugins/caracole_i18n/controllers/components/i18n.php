<?php
/**
 *	I18n
 *	I18n component.
 *	Will guess the best language in which display the website as well as changing language whenever needed.
 **/
class I18nComponent extends Object {

	/**
	 *	initialize
	 *	Fired right before the controller beforeFilter method
	 *
	 *	When accessing the website with the default language and with no language saved in the session, we try to guess
	 *	the best language and redirect the user.
	 **/
	function initialize(&$controller) {
		$this->controller = &$controller;

		$defaultLanguage = Configure::read('I18n.default');
		$currentLanguage = Configure::read('Config.language');

		// First visit, with no language explicitly set.
		if ($defaultLanguage==$currentLanguage && !$this->controller->Session->check('Config.language')) {
			// Getting default browser lang
			$preferredLanguage = $this->getPreferedLanguage();

			// That lang must be available
			if (in_array($preferredLanguage, Configure::read('I18n.languages'))) {
				// Saving in session to stop making the test and saving application-wide
				$controller->Session->write('Config.language', $preferredLanguage);
				$currentLanguage = $preferredLanguage;
				Configure::write('Config.language', $currentLanguage);

				// Redirecting to the same page in this new language
				if ($currentLanguage!=$defaultLanguage) {
					return $this->controller->redirect(null);
				}
			}

		}

		// Subsequent visits, simply saving the current language
		$controller->Session->write('Config.language', $currentLanguage);

		// We also save in a configure variable the url to access the homepage in this language
		$defaultUrl = Configure::read('SiteUrl.default');
		Configure::write('SiteUrl.lang', ($currentLanguage==$defaultLanguage) ? $defaultUrl : $defaultUrl.$currentLanguage.'/');
	}

	/**
	 *	getPreferedLanguage
	 *	Returns the user prefered language based on its browser configuration.
	 **/
	function getPreferedLanguage() {
		$l10n = $this->__getL10nModel();
		$l10n->get();
		$catalog = $l10n->catalog($l10n->lang);
		$lang = empty($catalog['localeFallback']) ? Configure::read('I18n.default') : $catalog['localeFallback'];
		return $lang;
	}

	/**
	 *	__getL10nModel
	 *	Returns the cakePHP L10n model used for localization and configuration
	 **/
	function __getL10nModel() {
		return ClassRegistry::init('L10n');
	}


}
