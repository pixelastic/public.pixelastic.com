/**
 *	Caracole Charmap plugin
 *	Displays a list of special characters not easily accessible on a keyboard.
 **/
(function() {
	var pluginName = 'charmap';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// Register the command
			editor.addCommand('mcecaracole_'+pluginName, function() {
				// Opening the charmap window
				$.ui.dialog.alert('http://'+document.domain+'/editors/charmap', {
					title: editor.getLang('caracole_charmap.desc'),
					width:600,
					open: function(event, ui) {
						// References to objects
						var self = $(this),
							list = self.find('ul'),
							preview = self.find('.preview');

						// We bind both event to the main ul to avoid creating to much event handlers
						list
						.click(function(event) {
							// Insert content on click and close the dialog box
							editor.execCommand('mceInsertContent', false, event.target.innerHTML);
							self.dialog('close');
							return false;
						})
						.mousemove(function(event) {
							// Shwo a preview of the cell on hover
							preview.html(event.target.innerHTML);
						});
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