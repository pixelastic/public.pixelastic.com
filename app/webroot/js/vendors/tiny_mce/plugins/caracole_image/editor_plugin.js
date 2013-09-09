/**
 *	Caracole Image plugin
 *	Allow for integrating images. Binded to the Caracole filebrowser
 **/
(function() {
	var pluginName = 'image';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// Register the command
			editor.addCommand('mcecaracole_'+pluginName, function() {
				$.ui.dialog.alert('Work in progress');
			});

			// Adding a button
			editor.addButton(pluginName, {
				title : editor.getLang('caracole_image.title'),
				cmd : 'mcecaracole_'+pluginName
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