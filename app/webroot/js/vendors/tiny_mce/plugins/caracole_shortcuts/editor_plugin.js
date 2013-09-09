/**
 *	Caracole Shortcut plugin
 *	Adds keyboard shortcuts for some common tasks
 *	- Ctrl+S saves the page
 *	- See the caracole_tabindex plugin to way to handle tab key
 **/
(function() {
	var pluginName = 'shortcuts';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// We find the textarea id
			var textareaId = editor.getParam('fullscreen_is_enabled') ? editor.getParam('fullscreen_editor_id') : editor.editorId;

			// Save function
			var saveFormFunction = $('#'+textareaId).closest('form').data('saveFormFunction');
			// Adding shortcut
			if (saveFormFunction) {
				editor.onKeyDown.add(function(editor, event) {
					if (!(event.keyCode==83 && event.ctrlKey)) return true;
					saveFormFunction();
					tinymce.dom.Event.cancel(event);
					return false;
				});
			}



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