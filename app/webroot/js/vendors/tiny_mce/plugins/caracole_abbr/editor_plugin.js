/**
 *	Caracole Abbr plugin
 *	Allow for defining an <abbr> tag to insert explanation about a given word
 **/
(function() {
	var pluginName = 'abbr';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// Register the command
			editor.addCommand('mcecaracole_'+pluginName, function() {
				// References
				var selection = editor.selection.getNode(),
					selectedText = editor.selection.getContent(),
					abbr = $(editor.dom.getParent(selection, 'abbr'));

				// Data to pass to the form
				var ajaxData = {};
				// Taking existing title
				if (abbr.length) {
					var title = abbr.attr('title');
					ajaxData.title = title;
				}


				// Opening the code window
				$.ui.dialog.alert('http://'+document.domain+'/editors/abbr', {
					title: editor.getLang('caracole_abbr.title'),
					width:475,
					open: function(event, ui) {
						// Reference to elements
						var self = $(this),
							form = self.find('#EditorAbbrForm'),
							remove = form.find('button.remove'),
							cancel = form.find('button.cancel');

						// We hide the remove button if we're adding an abbreviation
						if (!abbr.length) {
							remove.hide();
						}

						// Cancel button
						cancel.click(function() { self.dialog('close'); return false; });

						// Remove button
						remove.click(function() {
							// Removing the parent and replacing with the text content
							if (abbr.length) {
								$(selection).replaceWith(selection.innerHTML);
							}
							// Updating node change
							editor.nodeChanged();
							self.dialog('close');
							return false;
						});

						// Update
						form.submit(function() {
							var title = $('#EditorTitle', form).val();

							// Do nothing if not title set
							if (title==='') {
								remove.click();
								return false;
							}

							// Creating the abbr element if none is set
							if (!abbr.length) {
								var uniqueId = editor.dom.uniqueId();
								editor.execCommand('mceInsertContent', false, '<abbr id="'+uniqueId+'">'+selectedText+'</abbr>');
								abbr = $(editor.dom.select('#'+uniqueId)).attr('id', null);
							}

							// title
							editor.dom.setAttrib(abbr[0], 'title', title);

							// Updating node change
							editor.nodeChanged();

							self.dialog('close');
							return false;
						})

					},
					ajax: {
						type: 'post',
						data: ajaxData
					}
				});
			});

			// Adding a button
			editor.addButton(pluginName, { title : 'caracole_'+pluginName+'.desc', cmd : 'mcecaracole_'+pluginName });

			// Update on node change
			editor.onNodeChange.add(function(editor, controlmanager,node) {
				// Selecting an abbr
				var isAbbr = $(node).closest('abbr').length;
				// Selecting a text
				var textSelected = editor.selection.getContent()!=='';

				// Disabled if neither an abbr nor text selected
				controlmanager.setDisabled('abbr', !isAbbr && !textSelected);
				// Active if selecting an abbr
				controlmanager.setActive('abbr', isAbbr);
			});
		},

		/**
		 *	getInfo
		 **/
		getInfo : function() {
			return {
				longname : 'caracole_'+pluginName,
				author : 'Pixelastic',
				authorurl : 'http://www.pixelastic.com/',
				infourl : 'http://www.pixelastic.com/',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('caracole_'+pluginName, tinymce.plugins['caracole_'+pluginName+'Plugin']);
})();