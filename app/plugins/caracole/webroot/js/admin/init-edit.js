/**
 *	admin_edit Javascript methods
 *
 *	Will :
 *		- update the form to use the jQuery UI tabs
 *		- use jQuery UI datepicker for datetime fields
 *		- use custom formHabtm method to select habtm relationships
 *		- Enable auto-save on Ctrl+S (saving tinyMCE text when necessary)
 *
 *	We did not use the great jQuery form plugin for this part because we do not want to autosave file uploads.
 *	They already are handled by SWFUpload, we only need to upload the resulting value and if we use the default jQuery
 *	form behavior, it will upload the files no matter what.
 *	Instead, we use its handy formSerialize() method on the needed fields and use a good ol' $.ajax submit instead.
 *
 *
 *	TODO : Autosave while in add mode (at least on posts) will ask the browser to download JSON. Need fix.
 **/
$(function() {
	var form = $('#adminEdit form.editForm');
	if (!form.length) return;
	var fieldsets = $(".fieldsets", form);

	// Adding tabs to the add and edit forms
	if ($('ul.tabMenu', fieldsets).length) {
		fieldsets.tabs();
	}


	// Datepicker
	(function() {
		form.find('.datetime').each(function() {
			var field 		= $(this),
				inputFull	= field.find('input.full')
				inputDate 	= field.find('input.date'),
				inputTime	= field.find('input.time')
			;
			// Displaying datepicker on the date input
			inputDate.datepicker({
				'showAnim' : 'fadeIn',
				'dateFormat' : 'yy-mm-dd',
				// Force the datepicker to show above the SWFUploader (z-index:10)
				'beforeShow': function(input, datepicker) {
					setTimeout(function() {
						$(datepicker.dpDiv).css('zIndex', 100);
					}, 500);
				}
			});
			// Setting the dateDate and dateTime values
			var splitDate = inputFull.val().split(' ');
			if (splitDate!="") {
				if (splitDate[0]!='0000-00-00') inputDate.val(splitDate[0]);
				if (splitDate[1]!='00:00:00') inputTime.val(splitDate[1].substring(0, 5));
			}

			// Updating the full date whenever the other two fields are updated
			var updateFull = function() {
				inputFull.val(inputDate.val()+' '+inputTime.val());
			}
			inputDate.change(updateFull);
			inputTime.change(updateFull);
		});



	})();

	// Habtm
	form.find('.habtm select').formHabtm({

	});

	// Selecting first form field
	form.find(':input[tabindex=1]').focus();


	// Saving content on Ctrl + S and autosave
	(function() {
		// Getting the hidden field storing the id
		var idField = form.find('input[type=hidden][name$=[id]]');
		var add = !idField.val();

		// Getting all tinyMCE instances and saving content to textarea
		function updateEditorContent() {
			form.find(':tinymce').each(function() {
				tinyMCE.get(this.id).save();
			});
		}

		// Update modified date
		var modifiedDatePlaceholder = $('#modifiedDatePlaceholder');
		if (!modifiedDatePlaceholder.length) {
			modifiedDatePlaceholder = $('<div id="modifiedDatePlaceholder" class="lastSave"></div>').insertAfter(fieldsets);
		}

		// This is the method to save the form. It will be called either by Ctrl+S or by an automatic save
		// We will make sure to correctly save the tinyMCE zones back into their corresponding textarea
		// We will also not save file upload fields because as they are done asynchronously, it is useless.
		// We only save the associated id fields.
		// We won't be able to use ajaxSubmit here because it will force the file upload, so we need to manually
		// generate our data to be saved
		// We also have to take care of the cake Security component. It will complain that the file are not present in its data
		// so we will add fake upload data that will satisfy him
		function saveForm(options) {
			// We start by saving tinyMCE content in textareas
			updateEditorContent();

			// We grab all the fields we need to save
			var data = form.find(':input:not(:file):not(:button)').fieldSerialize();

			// We also create dummy fields for each field to make the Security component happy
			var fileData = {},
				dummyKeys = ['error', 'name', 'size', 'tmp_name', 'type'];
			form.find(':file').each(function() {
				// Adding dummy keys
				for(var i in dummyKeys) fileData[this.name+'['+dummyKeys[i]+']'] = '';
			});
			// Appending to existing data
			data+='&'+$.param(fileData);

			var settings = $.extend(
				{
					url: form.attr('action')+'.json',
					type:'POST',
					data : data,
					success: function(data) {
						if (data.error) return;
						//Update modified data
						modifiedDatePlaceholder.html(data.modified);
						// Saving a new item, we change the url to edit/
						if (add) {
							// Setting the id, changing the posted url
							idField.val(data.id);
							form.attr('action', data.url);
							//Updating the security token
							if (data.secure) {
								form.find("input[name='data[_Token][fields]']").parent('div').replaceWith(data.secure);
							}
						}
					}
				},
				options
			);
			// Performing the request
			$.ajax(settings);
		}

		// We save that method in the form data, so it is accessible globally (tinyMCE will need it)
		form.data('saveFormFunction', saveForm);

		// Ctrl + S will save the current form
		$(window).keydown(function(event) {
			if (!(event.keyCode==83 && event.ctrlKey)) return true;
			saveForm();
			return false;
		});

		// Only draftable items can benefit from the autosave
		var isDraftField = form.find(':checkbox[name$=[is_draft]]');
		if (!isDraftField.length) return;

		// Autosave function.
		function autosave() {
			// Only already drafted elements or new elements can be autosaved
			if (!(add || isDraftField.attr('checked'))) return;

			// Autosaving a new item, we force the draft flag
			if (add) {
				var isDraftFieldInitialValue = isDraftField.attr('checked');
				isDraftField.attr('checked', true);
			}

			// Saving the page in the background
			saveForm({
				global: false
			});

			// We reset the is_draft flag
			if (add) {
				isDraftField.attr('checked', isDraftFieldInitialValue);
			}
		}

		// Auto save every two minutes
		var autosaveInterval = setInterval(autosave, 120000);
	})();



});