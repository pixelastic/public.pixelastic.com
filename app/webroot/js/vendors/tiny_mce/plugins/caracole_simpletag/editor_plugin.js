/**
 *	Caracole Simpletag plugin
 *	Allow the creation of simple buttons to perform the default styles
 *	p,h3,h4 and h5 are accessible as simple buttons.
 *	We also add the code button, to wrap single lines of code. tinyMCE default its rendering to block, so make sure
 *	that you have defined a formats: { code: { inline:'code'} } in your tinyMCE.init
 **/
(function() {
	var pluginName = 'simpletag';

	tinymce.create('tinymce.plugins.caracole_'+pluginName+'Plugin', {
		/**
		 *	init
		 **/
		init : function(editor, url) {
			// List of simple tags
			var simpleTags = ['p','h3','h4','h5','code'];

			// Those two method simply return a function with a given parameter inside their closure
			function getCommand(tag) { return function() { editor.formatter.toggle(tag, undefined); } }
			function getOnNodeChange(tag) {
				// Special onNodeChange method for the <code> element
				if (tag=='code') {
					return function(editor, controlmanager,node) {
						// We won't activate if the code parent is a pre element
						var setActive = (
							(node.nodeName == tag.toUpperCase())
							&&
							node.parentNode.nodeName.toUpperCase()!='PRE'
						);
						controlmanager.setActive(tag, setActive);
					}
				}
				// Classic onNodeChange method
				return function(editor, controlmanager,node) {
					controlmanager.setActive(tag, node.nodeName == tag.toUpperCase());
				}
			}

			// Looping for each tag
			var tag;
			for(var i in simpleTags) {
				tag = simpleTags[i];
				// Adding commands
				editor.addCommand('mcecaracole_'+tag, getCommand(tag));
				// Registering button
				editor.addButton(tag, {
					title : 'caracole_simpletag.'+tag+'_desc',
					cmd : 'mcecaracole_'+tag
				});
				// Update on node change
				editor.onNodeChange.add(getOnNodeChange(tag));
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