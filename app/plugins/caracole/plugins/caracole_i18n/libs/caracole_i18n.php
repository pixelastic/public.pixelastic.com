<?php
/**
 *	CaracoleI18n
 *
 *	- In bootstrap level :
 *		Default language and other available languages are defined in bootstrap. We need to put it here because this is
 *		the earliest place in the dispatch chain we can use. And it still can be edited on a app-specific basis without
 *		having to dig into the Caracole plugins.
 *
 *		We then call CaracoleI18n::init() in the Caracole bootstrap. That way we force Configure(Config.language) as
 *		early as possible. The value is based on the 3-letter code present in the url (and in the available language) list,
 *		or revert to the default language if none is found.
 *
 *		We rake care of removing the language from the url, to allow the routing process to correctly dispatch to the
 *		correct action.
 *		We also save the corresponding 2-letter iso code for later use and set corresponding PHP locales.
 *
 *		Once this is done, we can safely load all our config.php files and start the dispatching : loading our models,
 *		controllers and so on.
 *
 *	- In controller level :
 *		In our I18n component we make a simple test on beforeFilter. We need to redirect new users to the language that
 *		best suits them. To do so, we will check their Accept-Language user agent string (thanks to the built-in L10n
 *		object, this is fairly easy), and redirect to the same page, but with the correct language set.
 *
 *		We also save the resulting language in Session to avoid making such a test on subsequent request.
 *
 *		We will also override CaracoleAppController::redirect to manually prepend the language iso3 code when redirecting.
 *
 *	- In view level :
 *		We first override the CaracoleAppHelper::url method to, correctly prepend the language code to each url if needed,
 *		just like we did with Controller::redirect.
 *
 *		We will also set the correct lang and xml:lang attributes to the HTML header. And setting the correct Identifier-URL.
 *		And finally, we set the correct language on tinyMCE
 *
 *
 *	TODO : Add links to switch language easily on every page
 *	TODO : Create a behavior and table that will get the correct translation from the database
 *	TODO : Allow the admin panel to easily translate each item when editing it
 *	TODO : Handle the slug cases where a same page can have different url in different languages
 *
 **/
class CaracoleI18n extends Object {

	/**
	 *	init
	 *	Set the current language based on the url
	 **/
	function init() {
		$defaultLang = $lang = Configure::read('I18n.default');
		$languageInUrl = false;
		// Finding lang in url
		do {
			// Default lang on index
			if (empty($_GET['url'])) break;

			// Default lang if no lang set
			if (!preg_match('/^([a-z]{3})(\/?)$/i', substr($_GET['url'],0,4), $matches)) break;

			// Default lang if found lang does not exist
			if (!in_array($matches[1], Configure::read('I18n.languages'))) 	break;

			// Lang found in url
			$lang = $matches[1];
			break;
		} while (false);

		// We set this language as the default one
		Configure::write('Config.language', $lang);

		// Timezone
		date_default_timezone_set(Configure::read('I18n.DefaultTimeZone'));

		// Loading the L10n object
		App::import('L10n');
		$l10n = new L10n();

		// Getting the iso2 language code
		$iso2 = $l10n->map($lang);
		Configure::write('Config.languageIso2', $iso2);

		// Setting locales, with multiple variations to handle various OS
		$catalog = $l10n->catalog($lang);
		$locales = array(
			$iso2.'_'.strtoupper($iso2).'.'.strtoupper(str_replace('-', '', $catalog['charset'])),	// fr_FR.UTF8
			$iso2.'_'.strtoupper($iso2),	// fr_FR
			$catalog['locale'],				// fre
			$catalog['localeFallback'],		// fre
			$iso2							// fr
		);
		setlocale(LC_TIME, $locales);	// Date display
		setlocale(LC_CTYPE, 'C');		// Order and lowercase/uppercase

		// We remove the lang from the url if one is set
		if ($lang!=$defaultLang) $_GET['url'] = substr($_GET['url'], 4);
		if (empty($_GET['url'])) $_GET['url'] = '/';
	}


	/**
	 *	strftime
	 *	Wrapper of the php strftime method to take care of a Windows bug that prevent month names
	 *	using accent to be correctly displayed.
	 *	We need to force the utf-8 encoding on this platform
	 **/
	function strftime($format, $date = null) {
		// On Windows, we will force the utf8 encoding of the date
		if (DIRECTORY_SEPARATOR == '\\') {
			return utf8_encode(strftime(utf8_decode($format), $date));
		}
		// On linux, this is already taken care of by setlocale()
		return strftime($format, $date);
	}

}
?>