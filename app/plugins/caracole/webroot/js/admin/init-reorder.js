/**
 *	admin_reorder Javascript methods
 **/
$(function() {

	$('form.reorder ul.itemList').sortable({
		axis: 'y',
		placeholder: 'placeholder',
		update: function(e, ui) {
			// Reorder and submit
			$('li input', e.target).each(function(i) { this.value = i+1; });
			var form = $(e.target).closest('form');
			form.ajaxSubmit({
				url:form.attr('action')+'.json'
			});
		}
	}).disableSelection();

});