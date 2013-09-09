/**
 *	Admin login index
 **/
$(function() {

	// Switching from OpenId and classic login form
	$('#loginForm a.goToOpenId, #loginForm a.goToClassic').click(function() {
		var thisFieldset = (this.hash=='#fieldsetOpenId') ? '#fieldsetClassic' : '#fieldsetOpenId'
		$(this.hash).toggle();
		$(thisFieldset).toggle()
		return false;
	});
	// Prefilling the OpenId field while clicking on a provider
	$('#loginForm div.providers ul a').click(function() {
		$('#ajaxIndicator').data('overlay', $.ui.dialog.overlay.create(null)).addClass('pending');
		$('input[name$=\\[openid\\]]').val(this.href);
		$(this).closest('form').submit();
		return false;
	});

	// As of October 2010, Opera security settings prevent a third party website to create cookies.
	// This does break our implementation (and many other on the web) of the openId system
	// There IS a solution (stackoverflow works perfectly, and blazingly fast), but I couldn't figure out how to do it (yet)
	// So, instead of having our Opera users blocked forever on an openId page, I will hide the openId action to them
	if ($.browser.opera) {
		$('#loginForm a.goToOpenId').hide();
	}


});