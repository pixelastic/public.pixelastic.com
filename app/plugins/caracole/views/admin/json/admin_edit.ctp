<?php
/**
 *	admin_edit
 *	The admin_edit method is used to save the current item
 **/
$modelName = Inflector::classify($this->params['controller']);

// Passing validation errors as message if present
if (!empty($this->validationErrors)) {
	$this->set(array(
		'error' => true,
		'message' => $this->Fastcode->validationErrors($this->validationErrors)
	));
}

/**
 *	When saving a new item, we need to regenerate the security token.
 *	The security token is different when adding a new item than when editing one.
 *
 *	We will then create a whole new form, with the id specified, re-add every fields, and call
 *	$this->Form->secure() to get the correct security token.
 *
 *	We will get it back to the view in JSON and update it in the form to allow subsequent saves
 **/
$url = Router::url(array('action' => 'edit', 'id' => $this->data[$modelName]['id']));
$pageParams = Router::parse($this->here);
if ($pageParams['action']=='add') {
	$fieldList = array();
	$this->Form->create($modelName, array('url' => $url));
	foreach($fields as $fieldName => &$fieldOptions) {
		$this->Fastcode->input($fieldName, $fieldOptions);
	}
	$this->Form->fields[$modelName.'.id'] = $this->data[$modelName]['id'];
	$secure = $this->Form->secure($this->Form->fields);
} else {
	$secure = false;
}


// Returning the id of the saved element
$this->set('data', array(
	'secure' => $secure,
	'id' => $this->data[$modelName]['id'],
	'modified' => sprintf(__d('caracole', 'Last save at %1$s', true), date('H\hi')),
	'url' => $url
));
