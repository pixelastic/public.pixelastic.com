/**
 *	Adding a new comment a preview
 **/
$(function() {
	// Submitting form
	$('#CommentAddForm').live('submit', function() {
		var form = $(this);

		// Submitting the form
		form.ajaxSubmit({
			url: form.attr('action')+'.json',
			success: function(data) {
				// Replace the form anyway
				form.replaceWith(data.form);
				// Cleaning space and adding the comment
				if (data.comment) {
					$('div.commentIndex div.noComments').remove();
					$('#commentPreview').remove();
					$('div.commentList').append(data.comment);
				}
			}
		});
		return false;
	});

	// Comment preview
	$('#CommentAddForm button.preview').live('click', function() {
		// Submitting the form to the preview action
		var form = $(this).closest('form');
		form.ajaxSubmit({
			url: form.attr('action').replace('add', 'preview')+'.json',
			success: function(data) {
				// Cleaning space if needed
				$('div.commentIndex div.noComments').remove();

				// Add a new preview comment
				var previewComment = $('#commentPreview');
				var newComment = $(data.comment);
				if (previewComment.length) {
					previewComment.replaceWith(newComment);
				} else {
					newComment.appendTo('div.commentList');
				}
				// TODO : Move the scroll to the new comment
			}
		});
		return false;
	});

	// Deleting comments
	$('div.commentList a.commentDelete').live('click', function() {
		var link = $(this),
			comment = link.closest('.commentDisplay');

		$.ajax({
			url: link.attr('href')+'.json',
			success: function(data) {
				comment.remove();
			}
		})
		return false;
	});

	// Flagging/Unflagging comments
	$('div.commentList a.commentSpam').live('click', function() {
		var link = $(this),
			comment = link.closest('.commentDisplay');

		$.ajax({
			url: link.attr('href')+'.json',
			success: function(data) {
				comment.replaceWith(data.comment);
			}
		})
		return false;
	});

});