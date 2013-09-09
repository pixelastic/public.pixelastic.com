<?php
/**
 *	Packer default settings
 *
 *	Files will be loaded in the following order :
 *
 *		1/ Defined in Packer.default
 *		2/ Defined in Packer (usually in plugins)
 *		3/ Defined in the views/layout
 *
 *	I18n javascript files will be prepended to the js file to make sure they are in first
 *
 **/
CaracoleConfigure::write(array(
	'Packer' => array(

		/**
		 *	The list of default files that should always be loaded first.
		 *	All files defined in plugin/config.php will be lo
		 **/
		'default' => array(
		   // CSS for admin actions
		   'cssAdmin' 			=> array(),

			// CSS when debug is > 0
		   'cssDebug' => array('Caracole.debug'),

		   // Default CSS, used on every page
		   'cssDefault' => array(
				'Caracole.reset',		// Reseting default styles and applying common one
				'Caracole.common',		// Common classes used accross projects
				'Caracole.grid',		// Common grid elements : container, span, prepend and append classes
				'Caracole.print',		// Print stylesheet wrapped in @media print query
				'Caracole.js'			// Javascript-specific rules. Prefixed with .js
		   ),

		   // JS for admin actions
		   'jsAdmin' => array(),

		   // Js files when debug is > 0
		   'jsDebug' => array(
				'Caracole.vendors/prettyprint',		// More readable var debug output
			),

		   // Default JS, used on every page
		   'jsDefault' => array(
			   'http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js',		//	jQuery on Google's CDN
			   'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js',	// 	jQuery UI on Google's CDN
			   'Caracole.jquery-debug',		// Debug methods (toolbar and Firebug)

			   'Caracole.vendors/jquery-form',			//	Ajax form utilities.
			   'Caracole.ajax',							// Using Caracole Ajax events
			   'Caracole.init',							// Default init scripts
		   ),
		),

		// You can use these keys in your Configure calls
		'cssAdmin' 			=> array(),
		'cssDebug' 			=> array(),
		'cssDefault' 		=> array(),
		'jsAdmin' 			=> array(),
		'jsDebug' 			=> array(),
		'jsDefault' 		=> array(),
	)
));
