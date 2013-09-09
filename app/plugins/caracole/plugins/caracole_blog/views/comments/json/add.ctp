<?php
	/**
	 *	Adding a comment through AJAX
	 **/

// Successfully adding new comment
$comment = empty($this->validationErrors) ? $this->element('../comments/display', array('plugin' => 'caracole_blog', 'item' => $item['Comment'])) : null;

// Getting the form
$form = $this->element('../comments/add', array(
	'plugin' => 'caracole_blog',
	'post_id' => $item['Comment']['post_id']
));

// Returning the html to display
$this->set('data', array(
	'error' => !empty($this->validationErrors),
	'form' => $form,
	'comment' => $comment
));