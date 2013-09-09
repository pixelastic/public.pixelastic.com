/**
 *	formDocument
 *
 *	Adds asynchronous upload capability to form. Will present the user with a nice progress bar while its upload is in progress.
 *
 *	TODO : Should check that flash is loaded, or display an error message
 *	TODO : Should also accept Firefox drag'n'drop feature
 **/
(function($) {

	$.fn.formDocument = function(settings) {
		var element = $(this),
			options = $.extend({}, $.fn.formDocument.defaults, settings),
			siteUrl = $('head meta[name=Identifier-URL]').attr('content');

		// Page base url
		if (!siteUrl) return this;

		// Max upload filesize allowed. Found from the MAX_FILE_SIZE hidden field if found
		if (!options.file_size_limit) {
			var maxFileSize = $('#MAX_FILE_SIZE').val();
			if (maxFileSize) options.file_size_limit = maxFileSize+' B';
		}

		// Fallback on classic alert if dialog boxes are not loaded
		if (!$.ui.dialog.error) {
			$.ui.dialog.error = function(text, title) {
				alert(text);
			}
		}

		// Alias for the translation method
		function __(key) { return $.i18n('formDocument', key); }

		// Creating element and binding events
		return element.each(function() {
			// References
			var inputElement 		= $(this),
				uploadElement		= inputElement.find('.upload'),
				// Placing SWFButton
				placeholderWrapper	= uploadElement.find('.swfUploadWrapper'),
				buttonPlaceholder 	= placeholderWrapper.find('span:first')[0],
				buttonUpload		= uploadElement.find('.buttonUpload'),
				// Value preview
				valueElement		= uploadElement.find('.value'),
				previewElement		= valueElement.find('.preview'),
				removeLink			= valueElement.find('a.remove'),
				// Classic fields
				idField				= uploadElement.find('input[type=hidden][name^=data]'),
				// Dynamic upload fields
				dynamicUpload 		= uploadElement.find('.dynamicUpload'),
				barElement			= dynamicUpload.find('.bar'),
				percentElement 		= dynamicUpload.find('.percent'),
				cancelLink			= dynamicUpload.find('a.cancel'),
				// Posted data
				sessionId			= uploadElement.find('input[type=hidden][name^=sessionId]').val(),
				sizeLimit			= options.file_size_limit || uploadElement.find('input[type=hidden][name^=sizeLimit]').val(),
				actionUrl			= uploadElement.closest('form').attr('action'),
				fieldName 			= idField.attr('name'),
				// Small changes depending if we're uploading a document or an image
				documentType		= inputElement.is('.image') ? 'image' : 'document',
				uploadUrl			= (documentType=='image') ? 'images/upload.json' : 'documents/upload.json',
				fileTypes			= (documentType=='image') ? '*.jpg;*.jpeg;*.png;*.gif;*.bmp;*.wbmp;*.tiff' : null,
				fileTypesDescription= (documentType=='image') ? __('imageFiles') : null
				//userAgent			= inputElement.find('input[type=hidden][name^=userAgent]').val(),
			;

			// We get the button dimensions. We make sure that it is visible to do so
			var _classes = uploadElement.attr('class');
			uploadElement.attr('class', 'upload');
			var buttonHeight = buttonUpload.outerHeight(),
				buttonWidth = buttonUpload.outerWidth();
			uploadElement.attr('class', _classes);

			// We delegate events on the placeholder to the "real" button
			placeholderWrapper
				.hover( function() { buttonUpload.addClass('hover'); }, function() { buttonUpload.removeClass('hover'); })
				.mousedown(function() { buttonUpload.addClass('active'); })
				.mouseup(function() { buttonUpload.removeClass('active'); });

			// Resetting the whole upload UI
			function resetUploadUI() {
				// Removing classes to elements
				uploadElement.removeClass('uploading finalizing finished');
				// Reseting progress bar to 0%
				barElement.css('width', '0%');
				percentElement.text('0 %');
				// Clearing preview
				previewElement.empty();
				// Clearing id
				idField.val('');
			}

			// fileQueueError : When an error occured right when selecting a file
			function fileQueueError(event, file, error) {
				switch(error) {
					// File too big
					case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
						$.ui.dialog.error(__('fileTooBig'), __('error'));
					break;
					// Empty file
					case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
						$.ui.dialog.error(__('fileEmpty'), __('error'));
					break;
					// Invalid filetype
					case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
						// We improve the fileType readibility
						var readableFileTypes = fileTypes.replace(/;\*/g, ', ').replace('*', '');
						$.ui.dialog.error(__('fileInvalid')+' '+readableFileTypes, __('error'));
					break;
					// Default
					default:
						$.ui.dialog.error(__('fileError') + error, __('error'));
					break;
				}
				return false;
			}

			// fileDialogComplete : File correctly selected
			var fileDialogComplete = function() {
				$(this).swfupload('startUpload');
				return true;
			}

			// uploadStart : File is sent to server
			function uploadStart(event, file) {
				resetUploadUI();
				uploadElement.addClass('uploading');
			};

			// uploadProgress : On each step of the upload
			function uploadProgress(event, file, bytes, maxBytes) {
				var percent = Math.floor((bytes/maxBytes)*100);

				// Upload finished, we mark as finalizing
				if (percent>=100) {
					percent=100;
					// If the finalizing process is too long, we add sample preview
					timeoutSamplePreview = setTimeout(function() {
						if (uploadElement.is('.finished')) return;
						uploadElement.removeClass('uploading').addClass('finalizing');
						previewElement.html(file.name);
					}, 1000);
				}
				// Updating UI
				barElement.css('width', percent+'%');
				percentElement.text(percent+' %');
			}

			// uploadError : An error occured while uploading. Mostly due to 404 error
			function uploadError(event, file, data, response) {
				resetUploadUI();
				$.ui.dialog.error(__('unableToConnect'), __('error'));
				return false;
			}

			// uploadSuccess : The upload succeed. We still need to parse its response
			function uploadSuccess(event, file, data, response) {
				// Getting JSON answer
				try { data = $.parseJSON(data); }
				//Not JSON
				catch(e) {
					resetUploadUI();
					return $.ui.dialog.error(__('serverResponseUnknown'), __('error'));
				}

				// JSON return error
				if (data.error) {
					if (timeoutSamplePreview) clearTimeout(timeoutSamplePreview);
					resetUploadUI();
					$.ui.dialog.error(data.message, { 'title' : __('error') });
					return false;
				}

				// Passing in finished mode
				uploadElement.removeClass('uploading finalizing').addClass('finished');
				// Getting the newly id based on the document type (document or image)
				if (documentType=="image") {
					idField.val(data.data.Image.id);
				} else {
					idField.val(data.data.Document.id);
				}
				previewElement.html(data.html);
				return true;
			};

			// Canceling the upload
			cancelLink.click(function() {
				resetUploadUI();
				inputElement.swfupload('cancelUpload', null, false);
				return false;
			});

			// Removing the current file
			removeLink.click(function() {
				resetUploadUI();
			});

			// Initiate the swfUpload on the specified element
			inputElement.swfupload({
				// Upload settings
				upload_url : 			siteUrl+uploadUrl,
				flash_url : 			siteUrl+options.flash_url,
				flash9_url : 			siteUrl+options.flash9_url,
				// File settings
				file_size_limit: 		sizeLimit,
				file_post_name:			options.file_post_name,
				file_types:				fileTypes,
				file_types_description:	fileTypesDescription,

				// Button settings
				button_placeholder: 	buttonPlaceholder,
				button_width : 			buttonWidth,
				button_height : 		buttonHeight,
				button_disabled : 		false,
				button_action:			SWFUpload.BUTTON_ACTION.SELECT_FILE,
				button_cursor : 		SWFUpload.CURSOR.HAND,
				button_window_mode: 	SWFUpload.WINDOW_MODE.TRANSPARENT,
				// Misc
				debug: 					options.debug,
				post_params: {
					'sessionId': sessionId,
					//'userAgent' : userAgent,
					'actionUrl' : actionUrl,
					'fieldName' : fieldName
				}
			})
			// Error when selecting a file (too big)
			.bind('fileQueueError', fileQueueError)
			// Files selected
			.bind('fileDialogComplete', fileDialogComplete)
			// Upload started
			.bind('uploadStart', uploadStart)
			// Updating the progress bar all along the way
			.bind('uploadProgress', uploadProgress)
			// An error occured during the upload
			.bind('uploadError', uploadError)
			// The upload succeeded
			.bind('uploadSuccess', uploadSuccess);
		});
	}

	// Default config values
	$.fn.formDocument.defaults = {
		'flash_url': 				'swf/swfupload.swf',
		'flash9_url': 				'swf/swfupload_fp9.swf',
		'file_size_limit':	 		null,
		'file_post_name':	 		'Filedata',
		'file_types':				null,
		'file_types_description':	null,
		'debug' : 					false

	}

})(jQuery);
