/**
 *	tinyMCE configuration
 *	All textareas will be converted to advanced editors, unless they have a class of noEditor.
 *
 *	TODO : Detecting and setting current language
 **/

// Plugin list
var tinyMCEPlugins = 'paste,fullscreen,caracole_abbr,caracole_charmap,caracole_codeblock,caracole_simpletag,caracole_help,caracole_hr,caracole_image,caracole_link,caracole_paste,caracole_quote,caracole_shortcuts,caracole_source,caracole_spellcheck,caracole_tabindex,caracole_toolbar';

tinyMCE_GZ.init({
	themes : 'advanced',
	languages : $('html').attr('lang'),
	plugins : tinyMCEPlugins,
	baseUrl: "http://"+document.domain+"/js/vendors/tiny_mce",	//	tinyMCE baseUrl, without trailing slash
	url: "http://"+document.domain+"/editors/packer",			//	Compressed js url
	cache: false
});



(function() {
	var documentBaseUrl = "http://"+document.domain+'/';
	tinyMCE.init({
		// Targeting textareas
		mode : "specific_textareas",
		editor_selector : 'editor',
		editor_deselector: 'noEditor',

		// General config
		language: $('html').attr('lang'),
		document_base_url: documentBaseUrl,

		/**
		 *	Styling
		 *	We defined a skin of caracole so that each tinyMCE styling class is prefixed with caracole, making it easier to custom style
		 *
		 *	tinyMCE also loads the tiny_mce/themes/advanced/skins/caracole/ui.css by default, unless you specify an editor_css file.
		 *	We want neither the default neither an other one because we already loaded all tinyMCE styles in tinymce.css.
		 *	So we'll set a caracoleDoNotInclude editor_css file as a placeholder, then in the setup method, we will manually
		 *	flag it as already included
		 *
		 *	We will also overwrite the css file used to style the editor content. So we define a content_css key to point to our
		 *	url that will pack the correct files. Unfortunatly, tinyMCE will insist in loading the skin content.css.
		 *	We will also mark this file as loaded in the editor.onPreInit callback
		 **/
		skin: 'caracole',
		editor_css: 'caracoleDoNotInclude',		//	Hack to not load this file
		content_css: 'editors/css_content', // Custom rules for the editable area
		setup: function(editor) {
			// Mark CSS files as already included
			tinymce.DOM.files[documentBaseUrl+'caracoleDoNotInclude'] = true;
			editor.onPreInit.add(function(editor) {
				editor.dom.files[editor.baseURI.toAbsolute(documentBaseUrl+'js/vendors/tiny_mce/themes/advanced/skins/caracole/content.css')] = true;
			});

			editor.onNodeChange.add(function(editor) {
				var editorContent = editor.getContent();
				if (editorContent==="") {
					// We set content as a <p> containing a placeholder, then we delete the placeholder to place the caret
					editor.setContent('<p><span id="__CaretPlacholder">Placeholder</span></p>');
					editor.selection.select(editor.dom.select('#__CaretPlacholder')[0]);
					editor.dom.remove(editor.dom.select('#__CaretPlacholder')[0]);
				}
			});


		},
		//popup_css: false,

		//editor_css: 'caracole/css/admin/ui.css',
		//popup_css
		//popup_css_add
		//editor_css
		//visual:false

		// Toolbar
		theme: 'advanced',
		theme_advanced_toolbar_location: 'top',
		theme_advanced_toolbar_align:'left',
		theme_advanced_buttons1: 'undo,redo,|,bold,italic,|,p,h3,h4,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,hr,|,toolbar',
		theme_advanced_buttons2: 'fullscreen,word,spellcheck,|,styleselect,cleanup,|,abbr,code,codeblock,quote,|,charmap,source,help',
		theme_advanced_buttons3: '',
		// Advanced formats in the dropdown menu
		style_formats: [
			{title: 'Exposant', inline: 'sup' },
			{title: 'Indice', inline: 'sub' },
			{title: 'Suppression', inline: 'del' },
			{title: 'Ajout', inline: 'ins' }
		],
		// We force the code to be displayed inline
		formats: {
			code: { inline:'code', remove: 'all'},
			pre: { block:'pre', remove: 'all'},
			abbr: { inline:'abbr', remove: 'all'}
		},
		plugins: tinyMCEPlugins,


		// Cleaning
		entity_encoding : "raw",		// Keep special chars except &amp;, &lt;, &gt; and &quot;
		verify_html: true,
		fix_table_elements: true,
		fix_list_elements: true,
		forced_root_block: false,		// Allow for creating top-level elements not wrapped in <p> (used for <blokcquote> and <img>)
		relative_urls : false,
		valid_elements: "@[id|class|style|title|dir<ltr?rtl|lang|xml::lang],"
						+"a[rel|rev|charset|hreflang|tabindex|accesskey|type|name|href|target|title|class],"
						+"strong/b,em/i,strike,u,#p,-ol[type|compact],-ul[type|compact],-li,br,"
						+"img[longdesc|usemap|src|border|alt=|title|width|height|align],"
						+"-sub,-sup,-blockquote[cite],"
						+"-table[border=0|cellspacing|cellpadding|width|height|align|summary],"
						+"-tr[rowspan|width|height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,caption"
						+"#td[colspan|rowspan|width|height|align|valign|scope],"
						+"#th[colspan|rowspan|width|height|align|valign|scope],"
						+"-div,-span,-code,-pre[lang],-address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],"
						+"dd,dl,dt,-cite[class],-abbr[title],-acronym,-del[datetime|cite],-ins[datetime|cite],"
						+"object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width|height|src|*],"
						+"script[src|type],map[name],area[shape|coords|href|alt|target],bdo,"
						+"button,col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|valign|width],"
						+"dfn,fieldset,form[action|accept|accept-charset|enctype|method],"
						+"input[accept|alt|checked|disabled|maxlength|name|readonly|size|src|type|value],"
						+"kbd,label[for],legend,noscript,optgroup[label|disabled],option[disabled|label|selected|value],"
						+"q[cite],samp,select[disabled|multiple|name|size],textarea[cols|rows|disabled|name|readonly],tt,var",

		// Clean pasted text from Word
		//paste_auto_cleanup_on_paste : true,
		//paste_remove_styles : true,
		//paste_remove_spans : true
		//valid_child_elements
		//fix_nesting
		//cleanup_on_startup: true



		// TODO : Test this configs
		//keep_styles: true,
		//gecko_spellcheck: true
		//class_filter: function() {}
		//browsers
		//urlconverter_callback
		//setup: function() {	}
		//onpageload
		//oninit
		//handle_node_change_callback
		//file_browser_callback
		//removeformat_selector
		//entity_encoding
		//constrain_menu


	});
})();