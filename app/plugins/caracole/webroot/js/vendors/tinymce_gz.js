/**
 *	tinyMCE_GZ
 *	We rewrite the tinyMCE_GZ class to use jQuery as well as using the Caracole Packer
 **/
var tinyMCE_GZ = {
	// Default settings
	defaults : {
		themes : '',		//	comma-separated list of themes
		plugins : '',		//	comma-separated list of plugins
		languages : '',		//	comma-separated list of languages
		cache : true,		//	If the result should be cached or not
		url:	false		//	Url of the compressor file. This url should return the compressed Javascript code
	},
	// Flag to know if the core is loaded
	coreLoaded : false,

	/**
	 *	init
	 *	Init all settings
	 **/
	init: function(options, callback, scope) {
		// If core is already loaded, we can stop
		if (this.coreLoaded) return false;

		// Settings
		this.settings = $.extend({}, this.defaults, options);



		// Posting data to the compressor
		var url = this.settings.url;
		delete this.settings.url;
		if (!url) return false;

		$.ajax({
			async: false,
			url: url,
			data: this.settings,
			dataType: 'script',
			global:false,
			type:'get',
			complete:function(xhr, status) {
				//tinymce.dom.Event.domLoaded = true;
			}
		});

		// Marking the core as loaded
		this.coreLoaded = true;
		return true;
	},


	/**
	 *	start
	 *	Fired once tinyMCE is loaded
	 **/
	start : function() {
		// Saving the tinyMCE baseUrl
		tinyMCE.baseURL = this.settings.baseUrl;

		// We now mark all the included files as already loaded, to avoid tinyMCE reloading them
		var languages = this.settings.languages.split(',');
		for(var i in languages) {
			tinymce.ScriptLoader.markDone(tinyMCE.baseURL+'/langs/'+languages[i]+'.js');
		}
		var themes = this.settings.themes.split(',');
		for(var i in themes) {
			tinymce.ScriptLoader.markDone(tinyMCE.baseURL+'/themes/'+themes[i]+'/editor_template.js');
			for(var j in languages) {
				tinymce.ScriptLoader.markDone(tinyMCE.baseURL+'/themes/'+themes[i]+'/langs/'+languages[j]+'.js');
			}
		}
		var plugins = this.settings.plugins.split(',');
		for(var i in plugins) {
			tinymce.ScriptLoader.markDone(tinyMCE.baseURL+'/plugins/'+plugins[i]+'/editor_plugin.js');
			for(var j in languages) {
				tinymce.ScriptLoader.markDone(tinyMCE.baseURL+'/plugins/'+plugins[i]+'/langs/'+languages[j]+'.js');
			}
		}
	}
}