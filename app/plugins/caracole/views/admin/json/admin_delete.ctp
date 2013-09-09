<?php
/**
 *	admin_delete
 *	We return the list of id that got deleted, to remove them from the DOM
 **/
$modelName = Inflector::classify($this->params['controller']);

// Applying option to selection
if (!empty($this->data['Options'])) {
	// We select the items and return their ids
	$selectedIds = array();
	foreach($this->data[$modelName] as $id => &$options) {
		if (empty($options['checked'])) continue;
		$selectedIds[] = $id;
	}
	$this->set('data', array('model' => $modelName, 'id' => $selectedIds));
}
