/**
 *	admin_index Javascript methods
 **/
$(function() {

	// We bind the events using the form id, allowing .live() to work
	var itemListForm = $('form.formIndex');
	var formId = itemListForm.attr('id');
	var searchForm = $('form.searchForm');


	/**
	 *	Search features
	 *	Adding an autocomplete, and if selecting a value, will directly go to that page
	 *	The source will be fetched only once, when we focus the field
	 *
	 *	The request will return a JSON object of all results with value and label keys filled with the input field name
	 **/
	if (searchForm.length) {
		searchForm.find('div.searchField input.autocomplete').each(function() {
			var input = $(this),
				fieldModelRegexp = /data\[(.*)\]\[(.*)\]/.exec(input.attr('name'))
				modelName = fieldModelRegexp[1],
				fieldName = fieldModelRegexp[2]
			;

				// Init the autocomplete on focus
				input.attr('autocomplete', 'off').bind('focus.search', function() {
					// Getting the autocomplete source and init it
					$.ajax({
						global: false,
						data: { fieldName: fieldName },
						url : 'http://'+document.domain+'/admin/editors/search/'+modelName+'.json',
						success: function(data) {
							// Init the autocomplete
							input.autocomplete({
								source: data.source,
								select: function(event, ui) {
									location.href = ui.item.url;
								}
							});
						}
					});
					// Unbinding the fetching
					input.unbind('focus.search');
				})
		});

		/**
		 *	Advanced search options
		 *	Will hide/show the advanced options on click on the dropdown menu
		 **/
		var advancedSearchDropdown = $('#advancedSearchDropdown');
		searchForm.find('.toggleAdvancedSearchDropdown').click(function() {
			advancedSearchDropdown.toggle();
			return false;
		});
	}




	/**
	 *	Checking checkboxes and applying effect
	 *	We use a closure to define private method and add the logic for finding checkboxes even after
	 *	an AJAX call (checkbox list is stored in linkWrapper data). It will fasten the checkbox selection by not running
	 *	the same call on each click and using a cached version and up-to-date with the AJAX pagination
	 *
	 *	All this handlers are added using live because the whole form will be updated on each pagination link
	 **/
	(function() {
		// Checking method
		function check(checked) {
			var link = $(this),
				parent = link.closest('div.itemListOptions'),
				checkboxes = parent.data('checkboxes');

			// No checkbox list in cache, we generate it
			if (!checkboxes) {
				checkboxes = $('table.itemList :checkbox');
				parent.data('checkboxes', checkboxes);
			}

			// Toggling
			if (checked=="toggle") {
				checkboxes.each(function() {
					var that = $(this);
					return that.attr('checked', !that.attr('checked'))
				});
				return false;
			}
			// Checking/unchecking
			checkboxes.attr('checked', checked);
			return false;

		}

		// Adding handlers
		$('#selectAll').live('click', function() { return check.call(this, true); });
		$('#selectNone').live('click', function() { return check.call(this, false); });
		$('#selectToggle').live('click', function() { return check.call(this, 'toggle'); });

		// Submitting form in AJAX
		$('#'+formId).live('submit', function() {
			var form = $(this);

			// We only apply the AJAX submit when deleting items
			if (form.find('#OptionsAction').val()!='delete') return true;

			// Submitting form
			form.ajaxSubmit({
				url: form.attr('action')+'.json',
				// Success callback
				success: function(data) {
					if (data.error) return false;
					for (var i in data.id) {
						$('#'+data.model+data.id[i]+'Checked').closest('tr').hide();
					}
					return true;
				}
			});
			return false;
		});

	})();


});