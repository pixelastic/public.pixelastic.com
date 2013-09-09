<?php
/**
 *	admin_reorder
 *	Reordering the pages. Returning the ordered array of id
 **/
$modelName = Inflector::classify($this->params['controller']);
$this->set('data', $this->data[$modelName]);
