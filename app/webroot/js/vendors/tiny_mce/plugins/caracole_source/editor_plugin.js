/**
 *	Caracole Source plugin
 *	Allow for direct viewing and editing of the HTML source
 **/
(function() {
	var pluginName = 'source';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// Register the command
			editor.addCommand('mcecaracole_'+pluginName, function() {

				// Opening the source window
				$.ui.dialog.alert('http://'+document.domain+'/editors/source', {
					title: editor.getLang('caracole_source.title'),
					width:760,
					open: function(event, ui) {
						// Reference to elements
						var self = $(this),
							form = self.find('#EditorSourceForm'),
							cancel = form.find('button.cancel'),
							source = form.find('#EditorSource');

						// Cancel button
						cancel.click(function() {
							self.dialog('close');
							return false;
						});

						// Update
						form.submit(function() {
							editor.setContent(source.val(), {source_view : true});
							self.dialog('close');
							return false;
						})

					},
					ajax: {
						type: 'post',
						data: {
							source: editor.getContent({source_view : true})
						}
					}
				});
			});

			// Adding a button
			editor.addButton(pluginName, { title : 'caracole_'+pluginName+'.desc', cmd : 'mcecaracole_'+pluginName });

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