<?php
/**
 *	admin_habtm
 *	Returns the id of the
 **/
$modelName = Inflector::classify($this->params['controller']);

// Passing validation errors as message if present
if (!empty($this->validationErrors)) {
	$this->set(array(
		'error' => true,
		'message' => $this->Fastcode->validationErrors($this->validationErrors)
	));
}

// Returning the id of the saved element
$this->set('data', array(
	'value' => $value,
	'id' => $id
));
