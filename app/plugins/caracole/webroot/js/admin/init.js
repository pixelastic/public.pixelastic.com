/**
 *	CaracoleAdminInit method
 **/
$(function() {

	//			SIDEBAR
	// Selecting correct link in menu
	var sidebar = $('#sidebar');
	$('a', sidebar).each(function() {
		// Stop if don't point to correct page
		if (this.href != window.location.href) {
			return this;
		}
		var link = $(this);
		// Link is in sublist, we mark the list as opened
		if (link.is('li.parent ul a', sidebar)) {
			link.closest('ul').prev('a').addClass('opened');
			return this;
		}
		// Link is simple, we mark it as active
		return link.addClass('currentPage');
	});

	// Opening sublist on click
	$('li.parent > a', sidebar).click(function() {
		$(this).toggleClass('opened');
		return false;
	});

	// Lightbox on images
	$('a.lightbox').live('click.lightbox', function(e) {
		e.preventDefault();
		$.slimbox(this.href, this.title);
	});

});