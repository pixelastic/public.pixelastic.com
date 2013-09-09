/**
 *	init.js
 *	Added to ALL pages
 **/
$(function() {
	// Replacing protected mails with real link
	$('.protectedMail').each(function() {
		var original = $(this);
		// Replacing each span with its real value
		original.find('.mailChar').each(function() {
			var character = $(this);
			character.replaceWith(character.attr('title'));
		});
		// Replacing the whole mail with a real link
		var mailAddress = original.text();
		original.replaceWith('<a href="mailto:'+mailAddress+'>'+mailAddress+'</a>');
	});


});