/**
 *	dialog
 *	Extends the jQueryUI dialog class to provide some more new methods
 *
 *	TOOD : Should pass second parameter of alert as a string as default tite
 *	TODO : Maybe creating a way of displaying error messages. Or adding a class to the dialog is enough ?
 **/
(function($) {
	// Private method to create elements to display dialog. Will re-use unused elements
	function getDialogElement(index) {
		if (!index) index = 1;
		var dialogElement = $('#dialogBox'+index);
		// Perfect, we create it
		if (!dialogElement.length) {
			return $('<div id="dialogBox'+index+'" />').appendTo(document.body);
		}
		// Already used ? we try the next one
		if (dialogElement.dialog('isOpen')) {
			return getDialogElement(index+1);
		}
		// We re-use that one
		return dialogElement.dialog('destroy').empty();
	}

	// Check if a given string is a url
	function isUrl(str) {
		var regex = /^((http|ftp|https):\/\/w{3}[\d]*.|(http|ftp|https):\/\/|w{3}[\d]*.)([\w\d\._\-#\(\)\[\]\\,;:]+@[\w\d\._\-#\(\)\[\]\\,;:])?([a-z0-9]+.)*[a-z\-0-9]+.([a-z]{2,3})?[a-z]{2,6}(:[0-9]+)?(\/[\/a-z0-9\._\-,]+)*[a-z0-9\-_\.\s\%]+(\?[a-z0-9=%&amp;\.\-,#]+)?$/;
		return regex.test(str);
	}


	$.fn.extend($.ui.dialog, {
		/**
		 *	alert
		 *	Convenient method to display in a dialog box a string, an html element or loading a page
		 **/
		alert: function(target, options) {
			// Element that will be turned into a dialog box
			var dialogElement = getDialogElement();
			var defaults = {
				dialogClass: '',
				modal: true,
				title: 'Caracole',
				zIndex: 300000,
				open: $.noop,
				ajax: {global:false}
			}
			// Use option as title if passed as a string
			if (typeof(options)=='string') {
				options = { title: options };
			}
			var settings = $.extend({}, defaults, options);

			// Default dialog for an element
			if (typeof target == 'object') {
				return $(target).dialog(settings);
			}

			// Simple text
			if (!isUrl(target)) {
				settings.dialogClass+=' ui-alert-text';
				return dialogElement.html(target).dialog(settings);
			}

			// Loading a page in AJAX
			var settingsLoading = {
				title: 			settings.title,
				dialogClass: 	'ui-alert-loading',
				modal:			settings.modal,
				zIndex:			settings.zIndex,
				open : function(event, ui) {
					// Setting default ajax options
					var settingsAjax = $.extend({
						complete: $.noop,
						dataType: 'html',
						global: false,
						url: target
					}, settings.ajax);
					// Loading the content when the file is loaded
					var _initialAjaxComplete = settingsAjax.complete;
					settingsAjax.complete = function(xhr, status) {
						// If the request complete without a status, it means it has been interrupted, so we'll stop
						if (xhr.status==0) return;
						// Firing initial event
						_initialAjaxComplete.apply(this, arguments);

						// Saving the initial open callback
						var _initialAlertOpen = settings.open;
						settings.open = function(event, ui) {
							// Firing the initial callback
							_initialAlertOpen.apply(this, arguments);
							// Focus on first form element
							dialogElement.find(':input:visible:first').focus();
						}

						// Closing the loading window and opening the result
						dialogElement.dialog('close');
						$.ui.dialog.alert(xhr.responseText, settings);
					}

					// Loading the page in AJAX
					dialogElement.pendingAjaxRequest = $.ajax(settingsAjax);
				},

				// Closing the loading window
				close: function(event, ui) {
					// Aborting a pending request (manually closing the loading window)
					if (dialogElement.pendingAjaxRequest.readyState!=4) {
						dialogElement.pendingAjaxRequest.abort();
					}
				}
			}
			return dialogElement.dialog(settingsLoading);

		},

		/**
		 *	error
		 *	Wrapper around dialog to display an error-like message
		 *
		 *	TODO : Make a better styling of those error messages. Ask Sacha
		 **/
		error: function(target, options) {
			if (typeof(options)=='string') options = { title: options };
			// Default options
			var settings = $.extend({
				dialogClass: 'ui-alert-error'
			}, options);
			// Wrapping text in error message
			target = '<p class="message error">'+target+'</p>';
			// Opening the dialog
			$.ui.dialog.alert(target, settings);

		}
	});
})(jQuery);
