/**
 *	init-document.js
 *
 *	Will add upload capabilities to forms
 **/
$(function() {
	$('form.niceForm div.input.document').formDocument({
		'upload_url': 'admin/documents/upload.json'
		//'debug' : true
	});

});