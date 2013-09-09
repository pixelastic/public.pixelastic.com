<?php
/**
 *	Cleanable
 *	Will clean a model insertion data before inserting in the database.
 **/
class CleanableBehavior extends ModelBehavior {

	/**
	 *	beforeSave
	 *	Will correct the most common issues when adding/editing items
	 **/
	function beforeSave(&$model) {
		foreach($model->data as $modelName => &$data) {
			foreach($data as $field => &$value) {
				//	We only clean existing fields
				if (!$model->hasField($field)) 	continue;

				$schema = $model->schema($field);

				//	Modifying fields based on type
				switch($schema['type']) {
					// Cleaning string and text
					case 'string':
					case 'text' :
						// We remove non-web chars
						$value = CaracoleInflector::cleanNonWebCharacters($value);
					break;
				}
			}
		}

		return true;
	}



}
