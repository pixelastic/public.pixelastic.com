/**
 *	Caracole Help plugin
 *	Little help information about the current tinyMCE and Caracole versions
 **/
(function() {
	var pluginName = 'help';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// Register the command
			editor.addCommand('mcecaracole_'+pluginName, function() {
				// Opening the help window
				$.ui.dialog.alert('http://'+document.domain+'/editors/help', {
					title: editor.getLang('caracole_help.title'),
					width:600,
					ajax: {
						type: 'post',
						data: {
							tinyMCE: {
								majorVersion: tinymce.majorVersion,
								minorVersion: tinymce.minorVersion,
								releaseData: tinymce.releaseData
							}
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