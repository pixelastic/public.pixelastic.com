<?php
/**
 *	admin_spam
 *	Toggle the spam flag of the given comment
 **/
// Returning the html to display
$this->set('data', array(
	'error' => false,
	'comment' => $this->element('../comments/display', array('plugin' => 'caracole_blog', 'item' => $item))
));
