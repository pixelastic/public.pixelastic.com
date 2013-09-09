/**
 *	Caracole Quote plugin
 *	Allow for quoting a text, and specifying its author.
 *	It will add the url source as a cite attribute and the author as an inner <cite class="author" /> element, added
 *	at the far end
 **/
(function() {
	var pluginName = 'quote';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// Register the command
			editor.addCommand('mcecaracole_'+pluginName, function() {
				// References
				var selection = editor.selection.getNode(),
					blockquote = $(editor.dom.getParent(selection, 'BLOCKQUOTE'));

				// We shouldn't be able to select/edit the author directly
				if ($(selection).is('cite.author')) {
					selection = blockquote;
				}

				// Data to pass to the form
				var ajaxData = {};
				// Taking existing data from the initial blockquote
				if (blockquote.length) {
					// Finding existing values
					var source = blockquote.attr('cite');
					var authorCite = blockquote.find('cite.author');
					// Default source and author value
					ajaxData.source = source;
					if (authorCite.length) { ajaxData.author = authorCite.html(); }
				}

				// Opening the quote window
				$.ui.dialog.alert('http://'+document.domain+'/editors/quote', {
					title: editor.getLang('caracole_quote.title'),
					width:475,
					open: function(event, ui) {
						// Reference to elements
						var self = $(this),
							form = self.find('#EditorQuoteForm'),
							remove = form.find('button.remove'),
							cancel = form.find('button.cancel');

						// We hide the remove button if we're adding a quote
						if (!blockquote.length) {
							remove.hide();
						}

						// Cancel button
						cancel.click(function() { self.dialog('close'); return false; });

						// Remove button
						remove.click(function() {
							// If a blockquote parent, we remove it
							if (blockquote.length) {
								blockquote.find('cite.author').remove(); // Removing the author
								// We either toggle the blockquote or remove the wrapping one depending if bloackquote is allowed
								// as a root element
								if (!editor.settings.forced_root_block) {
									editor.execCommand('mceRemoveNode', false, blockquote); // Removing the wrapping <blockquote>
								} else {
									editor.formatter.toggle('blockquote', undefined); // Toggling the blockquote on/off
								}
							}
							// Updating node change
							editor.nodeChanged();
							self.dialog('close');
							return false;
						})

						// Update
						form.submit(function() {
							// Getting values
							var source = $('#EditorSource', form).val();
							var author = $('#EditorAuthor', form).val();

							// Creating the blockquote if none is set
							if (!blockquote.length) {
								editor.formatter.toggle('blockquote', undefined);
								blockquote = $(selection).closest('blockquote');
								// We have to add a <p> after the <blockquote> if none is set
								var next = blockquote.next();
								if (!next.is('p')) {
									next = $('<p>&nbsp;</p>').insertAfter(blockquote);
								}
							}

							// The source in the cite element
							editor.dom.setAttrib(blockquote[0], 'cite', source);

							// Author in a cite element at the end of the blockquote
							var authorCite = $('cite.author', blockquote);

							if (!author) {
								// Removing element if no author
								authorCite.remove();
							} else {
								// Creating if non-existent
								if (!authorCite.length) authorCite = $('<cite class="author" />');
								// Setting value and adding to the end of the blockquote
								authorCite.html(author).appendTo(blockquote[0]);
							}
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
				controlmanager.setActive('quote', $(node).closest('blockquote').length);
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