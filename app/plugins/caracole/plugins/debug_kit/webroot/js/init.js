/**
 *	init
 *	Loading Javascript methods
 **/
$(function() {
	// Emulating debugKit toolbar default options.
	var debugKitToolbar = $('#debug-kit-toolbar');
	if (!debugKitToolbar.length) return;

	var toggleToolbar 		= $('#hide-toolbar').closest('li'),
		panels 				= $('#panel-tabs > li'),
		panelButtons		= panels.find('> a'),
		panelContents		= panels.find('.panel-content'),
		panelResizeRegions	= panelContents.find('.panel-resize-region'),
		clearDebug			= panelContents.find('a.clearDebug')
	;


	// Toggle whole bar
	toggleToolbar.click(function() {
		panels.not(this).toggle();
		return false;
	}).click();

	// Toggle one panel
	panelButtons.not('#hide-toolbar').click(function() {
		var thisContent = $(this).next('.panel-content');
		panelContents.not(thisContent.toggle()).hide();
		return false;
	});

	// Make neat arrays expandable
	panelContents.find('ul.neat-array ul.neat-array').each(function() {
		var ul				= $(this),
			parentLi 		= ul.parent('li');

		// We hide this sub array
		ul.hide();

		// We mark the parent li as expandable and closed and open its child on click
		parentLi
			.addClass('expandable collapsed')
			.click(function(e) {
				// Toggle the class
				parentLi.toggleClass('collapsed').toggleClass('expanded');
				// Toggle display of child element
				ul.toggle();
				return false;
			});
	});

	// Clear debug text
	clearDebug.click(function() {
		$(this).next().empty();
		return false;
	})




});