<?php
/**
 *	Default JSON response
 *	We will always return an JSON object.
 *	If a flash message is defined, we will return it.
 **/
// Flash message
if ($flashMessage = $this->Session->read('Message.flash')) {
	$message = $flashMessage['message'];
	$error = ($flashMessage['element']=='error');
	// Calling flash to remove the value from the session
	$this->Session->flash();
}
// Default options
$json = array(
	'error' => !empty($error),
	'message' => empty($message) ? '' : $message,
	'data' => empty($data) ? '' : $data
);


echo $this->Javascript->value($json);