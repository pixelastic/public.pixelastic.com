/**
 *	Caracole Link plugin
 *	Allow for integrating html links.
 *
 *	TODO : Create an url browser to easily fetch the website pages
 **/
(function() {
	var pluginName = 'link';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// Adding a button
			editor.addButton(pluginName, {
				title : editor.getLang('caracole_link.title'),
				cmd : 'mcecaracole_'+pluginName
			});

			// Update on node change
			editor.onNodeChange.add(function(editor, controlmanager,node) {
				// We activate the icon either when selecting a text, or when inside a link
				controlmanager.setActive('link', editor.selection.getNode() || $(node).closest('a').length);
			});

			// Register the command
			editor.addCommand('mcecaracole_'+pluginName, function() {
				// References
				var selection = editor.selection.getNode(),
					selectedText = editor.selection.getContent(),
					a = $(selection).closest('a');

				// Data to pass to the form
				var ajaxData = {};
				// Taking existing data from the initial link
				if (a.length) {
					ajaxData.href = a.attr('href');
					ajaxData.title = a.attr('title');
					if (a.attr('target')=='_blank') ajaxData.is_blank = 1;
				}

				// Opening the quote window
				$.ui.dialog.alert('http://'+document.domain+'/editors/link', {
					title: editor.getLang('caracole_link.title'),
					width:475,
					open: function(event, ui) {
						// Reference to elements
						var self = $(this),
							form = self.find('#EditorLinkForm'),
							remove = form.find('button.remove'),
							cancel = form.find('button.cancel');

						// We hide the remove button if we're adding a link
						if (!a.length) {
							remove.hide();
						}

						// Cancel button
						cancel.click(function(e) { e.preventDefault(); self.dialog('close'); });

						// Remove button
						remove.click(function(e) {
							e.preventDefault();
							// If a <a> parent, we remove it
							if (a.length) {
								a.replaceWith(a.html());
							}
							// Closing
							self.dialog('close');
						})

						// Update
						form.submit(function(e) {
							e.preventDefault();
							// Getting values
							var href = $('#EditorHref', form).val();
							var title = $('#EditorTitle', form).val();
							var is_blank = $('#EditorIsBlank', form).attr('checked');

							// If no href set, we consider that we remove the link
							if (!href) {
								remove.click();
								return false;
							}

							// Creating the a if none is set
							if (!a.length) {
								var uniqueId = editor.dom.uniqueId();
								editor.execCommand('mceInsertContent', false, '<a id="'+uniqueId+'">'+selectedText+'</a>');
								a = $(editor.dom.select('#'+uniqueId)).attr('id', null);
							}

							// Adding attributes. We MUST use the tinyMCE API here otherwise the href gets messed up
							editor.dom.setAttribs(a[0], {
								'href' : href,
								'title' : title,
								'target' : is_blank ? '_blank' : null
							});

							self.dialog('close');
						})

					},
					ajax: {
						type: 'post',
						data: ajaxData
					}
				});
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