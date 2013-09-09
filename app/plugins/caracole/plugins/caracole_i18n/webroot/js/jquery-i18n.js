/**
 *  jquery-i18n.js
 *
 *  jQuery I18n library to easily add internationalized strings into javascript apps
 *
 *  Based on the work by Maxime Haineault (http://haineault.com/blog/62/)
 *
 */

(function($){


    /**
     * $.i18n
     *
     * This method can accept various arguments and its behavior depends ont the number an type of them
     *
     * $.i18n('lang')
     *      Will set the default language as *lang*. All translations will then be found in that language.
     *      Default to html current language
     *      Eg. $.i18n('fre');
     *
     * $.i18n('lang.domain', {'key' : 'value})
     * 		Will save in the specified lang and domain the specified key and value pairs
     * 		You should at least create such an object for every lang, even the default one.
     * 		It is recommended to use string markers instead of real sentences. This will allow us to use the cakePHP
     * 		i18n extract feature more easily on the translation files.
     * 		Eg; $.i18n('fre.playback', { 'next' : 'suivant', 'previous' : 'precedent' }
     *
     *
     * 	$.i18n('domain', 'key')
     * 		Will return the translated string in the current language.
     * 		If no translation is found, the string marker will be returned
     * 		Eg; $.i18n('playback', 'next') will return 'suivant'
     *
     * 	The method is also chainable.
     *
     **/

	$.i18n = function() {
		// Setting default language
		if (!$.i18n.currentLang) $.i18n.currentLang = $('html')[0].lang;

		// Setting the current language
		if (arguments.length==1) {
			$.i18n.currentLang = arguments[0];
			return $.i18n;
		}

		// From now on, we need a string as first argument
		if (typeof(arguments[0])!='string') return this;

		// Saving a new translation object
		if (typeof(arguments[1]) == 'object') {
			var split 		= arguments[0].split('.'),
				lang 		= split[0],
				domain 		= split[1],
				translations= $.i18n.translations;

			// Creating the translation table
			if (!translations[lang]) translations[lang] = {};
			if (!translations[lang][domain]) translations[lang][domain] = {};
			$.extend(translations[lang][domain], arguments[1]);
			return $.i18n;
		}

		// Getting a translation
		if (typeof(arguments[1]) == 'string') {
			var domain 			= arguments[0],
				key				= arguments[1],
				currentLang 	= $.i18n.currentLang,
				translations	= $.i18n.translations;

			// Not found
			if (!translations[currentLang] || !translations[currentLang][domain] || !translations[currentLang][domain][key]) {
				return key;
			}

			return translations[currentLang][domain][key];
		}

		return this;
	}


	// Default settings
	$.i18n.translations = {};

})(jQuery);
