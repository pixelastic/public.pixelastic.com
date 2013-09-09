/**
 *	jQuery debug plugin.
 *	Methods to easily debug jQuery code. Will print code to the Firebug console if present, and will add them to
 *	the debugKit javascript tab.
 **/
(function($) {
	/**
	 *	debug
	 *	Prints information about any selected element
	 **/
	$.fn.debug = function() {
		return this.each(function(){
			$.log(this);
		});
	};

	/**
	 *	log
	 *	Main jQuery method to debug any var.
	 *	Will be outputted to both the console and the debug bar.
	 *	If a target is specified (as is the case when dealing with SWFUpload) then it will only be outputted to this element
	 **/

	 $.log = function(message, target) {
		//	Adding to Firebug console if set
		if (window.console && console.log) {
			console.log(message);
		}

		// We use a default target that we will cache in $.log.target
		if (!target) {
			target = $.log.target;
			if (!target) {
				target = $('#debugJavascriptPanel');
				if (target) {
					$.log.target = target;
				}
			}
		}

		if (!target) return;
		// Prettyfying message
		switch(typeof(message)) {
			// Complex var
			case 'object':
			case 'function':
			case 'array':
				message = (typeof(prettyPrint)=="undefined") ? message : prettyPrint(message);
			break;
			// Boolean
			case 'boolean':
				message = (message) ? '<p class="message success">true</p>' : '<p class="message error">false</p>';
			break;
			// Text
			case 'string':
			case 'number':
				message = '<p class="message">'+message+'</p>';
			break;
		}

		//	We add it to the page
		target.append(message);
	};


})(jQuery);
