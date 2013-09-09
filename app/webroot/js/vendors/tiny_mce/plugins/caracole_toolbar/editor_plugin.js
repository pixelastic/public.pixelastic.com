/**
 *	Caracole Toolbar plugin
 *	Allow for switching on/off the advanced toolbar
 **/
(function() {
	var pluginName = 'toolbar';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// Register the command
			editor.addCommand('mcecaracole_'+pluginName, function() {
				// Toggle advanced toolbar
				$('#'+editor.editorId+'_toolbar2').toggle();
				// Button active/inactive
				$('#'+editor.editorId+'_toolbar').toggleClass('mceButtonActive');
			});

			// Adding a button
			editor.addButton(pluginName, { title : editor.getLang('caracole_toolbar.desc'), cmd : 'mcecaracole_'+pluginName});

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