<?php
/**
 *	JSON Layout
 **/
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header('Content-Type: application/json');

// We search for a flash message to add to the response
if ($flashMessage = $this->Session->read('Message.flash')) {
	$message = $flashMessage['message'];
	$error = ($flashMessage['element']=='error');
	// Calling flash to remove the value from the session
	$this->Session->flash();
}

// $data is defined in views. It will be merged with default data
if (empty($data)) $data = array();
$data = Set::merge(
	array(
		'error' => !empty($error),
		'message' => empty($message) ? '' : $message,
	),
	$data
);

// JSON object
echo $this->Javascript->value($data);
