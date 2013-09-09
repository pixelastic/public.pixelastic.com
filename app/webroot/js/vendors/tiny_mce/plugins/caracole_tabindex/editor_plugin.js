/**
 *	Caracole Tabindex plugin
 *	Allow for tabbing inside and outside of the tinyMCE instance
 **/
(function() {
	var pluginName = 'tabindex';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// We set a tabindex value to the iframe instead of the initial textarea
			editor.onInit.add(function() {
				var editorId = editor.editorId;
				var textarea = $('#'+editorId);
				$('#'+editorId+'_ifr').attr('tabindex', textarea.attr('tabindex'));
				textarea.attr('tabindex', null);
			});

			// We hook on the tab key. One press will jump to the next focusable field. Maj+tab will insert a tab
			editor.onKeyDown.add(function(editor, event) {
				// We only listen for the tab key
				if (event.keyCode!=9) return;

				// Maj + tab will insert a tab
				if (event.shiftKey) {
					editor.execCommand('mceInsertContent', false, "\t");
					tinymce.dom.Event.cancel(event);
					return;
				}
				// Just pressing tab will jump to the next element
				var tabindex = $('#'+editor.editorId+'_ifr').attr('tabindex');
				// We get all the tabindexed elements of the page
				var inputs = [];
				$(':input[tabindex]').each(function() {
					inputs[$(this).attr('tabindex')] = this;
				});
				// We find the next after our element and focus it
				for (var position in inputs) {
					if (position<=tabindex) continue;
					inputs[position].focus();
					break;
				}

				tinymce.dom.Event.cancel(event);
				return;
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