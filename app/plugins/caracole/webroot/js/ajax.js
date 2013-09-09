/**
 *	Ajax global events handling.
 *
 *	Whenever an ajax request is pending, we block the main UI and add a loading indicator at the bottom of the page.
 *	We remove it when all requests have delivered.
 *	Whenever a request fail or succeed, we replace the indicator with the message.
 *
 **/
$(function() {
	// Default Ajax options
	$.ajaxSetup({'dataType': 'json', 'type': 'POST'})

	// Ajax indicator
	var ajaxIndicator = $('<div id="ajaxIndicator" class="ajaxIndicator"/>');
	ajaxIndicator
		// AJAX Start
		.bind('ajaxStart', function() {
			// Blocking UI and saving overlay for future reference
			ajaxIndicator.data('overlay', $.ui.dialog.overlay.create(null));
			// Styling as a loader
			ajaxIndicator.removeClass('success error').addClass('pending').empty();
		})
		// AJAX Success
		.bind('ajaxSuccess', function(event, xhr, options) {
			// Parsing return data, the callback does not return it
			var data = $.httpData(xhr, options.dataType);
			// Silently stop if no message
			if (!data || !data.message) return true;
			// Styling and setting the text
			ajaxIndicator.removeClass('pending').addClass(data.error ? 'error' : 'success').html(data.message).show();
			// Putting it under the view port and sliding it up
			var height = ajaxIndicator.outerHeight();
			ajaxIndicator.css('bottom', '-'+height+'px').animate({'bottom' : 0});

			// Hiding the success message after some time
			if (!data.error) {
				var hideTimeout = ajaxIndicator.data('hideTimeout');
				// If already set, we clear it
				if (hideTimeout) clearTimeout(hideTimeout);
				// Setting timeout to hide message
				hideTimeout = setTimeout(function() { ajaxIndicator.animate({'bottom': '-'+height+'px'}) }, 3000);
				ajaxIndicator.data('hideTimeout', hideTimeout);
			}
			return true;
		})
		// AJAX Error
		.bind('ajaxError', function(event, xhr, options, error) {
			// We get a readable error
			if (!error && xhr.statusText) error = xhr.statusText;
			if (!error) return false;

			// Displaying error
			ajaxIndicator.removeClass('pending').addClass('error').html(error).show();
			return true;
		})
		// AJAX Stop
		.bind('ajaxStop', function() {
			// Unblocking UI and removing pending state
			$.ui.dialog.overlay.destroy(ajaxIndicator.removeClass('pending').data('overlay'));

		})
		.appendTo(document.body);


});