/**
 *	Caracole Codeblock plugin
 *	Allow for adding a <pre><code> tag to wrap code
 *
 *	// TODO : Making text become code inside a list turns seems not possible 
 **/
(function() {
	var pluginName = 'codeblock';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// Register the command
			editor.addCommand('mcecaracole_'+pluginName, function() {
				// References
				var selection = editor.selection.getNode(),
					preblock = $(editor.dom.getParent(selection, 'PRE'))
					codeblock = preblock.find('code:first-child');;

				// Data to pass to the form
				var ajaxData = {};
				// Taking existing code language
				if (codeblock.length) {
					var language = codeblock.attr('lang');
					ajaxData.language = language;
				}

				// Opening the code window
				$.ui.dialog.alert('http://'+document.domain+'/editors/codeblock', {
					title: editor.getLang('caracole_codeblock.title'),
					width:475,
					open: function(event, ui) {
						// Reference to elements
						var self = $(this),
							form = self.find('#EditorCodeblockForm'),
							remove = form.find('button.remove'),
							cancel = form.find('button.cancel');

						// We hide the remove button if we're adding a code
						if (!codeblock.length) {
							remove.hide();
						}

						// Cancel button
						cancel.click(function() { self.dialog('close'); return false; });

						// Remove button
						remove.click(function() {
							// If a preblock parent, we remove it
							if (preblock.length) {
								// Moving content from the <code> to the <pre>, then removing the pre
								var content = codeblock.html();
								codeblock.remove();
								preblock.html(content);
								editor.formatter.toggle('pre', undefined);
							}
							// Updating node change
							editor.nodeChanged();
							self.dialog('close');
							return false;
						})

						// Update
						form.submit(function() {
							var language = $('#EditorLanguage', form).val();

							// Creating the preblock if none is set
							if (!preblock.length) {
								editor.formatter.toggle('pre', undefined);
								preblock = $(editor.selection.getNode());
								// Wrapping content in <code>
								codeblock = preblock.find('code:first-child');
								if (!codeblock.length) {
									codeblock = $('<code>'+preblock.html()+'</code>');
									preblock.empty().html(codeblock);
								}
								// We have to add a <p> after the <pre> if none is set
								var next = preblock.next();
								if (!next.is('p')) {
									next = $('<p>&nbsp;</p>').insertAfter(preblock);
								}
							}

							// Language
							codeblock.attr('lang', language);

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
				controlmanager.setActive('codeblock', $(node).closest('pre').find('code:first-child').length);
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