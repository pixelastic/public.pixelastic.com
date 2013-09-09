<?php
/**
 *	document
 *	This behavior is applied to all models but will be enabled only for those related to at least one Document
 *	It will automate the writing of find queries to make sure that Metadata are correctly fetched
 *
 */
class DocumentableBehavior extends ModelBehavior {
	// Models that are in fact documents
	var $documentModels = array('Document', 'Image');
	// Disabled models where the behavior should not fire
	var $disabledModels = array();
	// Type of relation that are to be checked to see if the behavior should be applied
	var $allowedAssociations = array('belongsTo', 'hasAndBelongsToMany');

	/**
	 *	setup
	 *	Loaded when applied to the model.
	 *	Will disable the behavior from the model if it is not related to a document
	 *	Will also disable for some named models that don't need it like Metadata
	 **/
	function setup(&$model, $config = array()) {
		// We make a first filter to allow Models related to the main model
		foreach($this->allowedAssociations as $association) {
			foreach($model->{$association} as $modelAlias => $modelOptions) {
				if (!in_array($modelOptions['className'], $this->documentModels)) continue;
				if ($model->name=='Metadata') continue;
				return true;
			}
		}

		// Saving the list of models that have this behavior disabled
		$this->disabledModels[] = $model->name;
		return false;
	}


	/**
	 *	beforeFind
	 *	Will automatically set the contain query field to accept Metadata
	 *	We will have to build a default contain key that will hold all related models if none is set
	 *	Then, we will add the contained Document and Metadata
	 **/
	function beforeFind(&$model, $query) {
		// Disabling the callback for disabled models
		if (in_array($model->name, $this->disabledModels)) return $query;

		// Fast fail if contain manually set to false
		if (isset($query['contain']) && ($query['contain']===false || $query['contain']<0)) return $query;

		// Create a default contain key if none is set
		if (empty($query['contain'])) {
			foreach($model->__associations as $association) {
				foreach($model->{$association} as $relatedModelName => &$relationshipOptions) {
					$query['contain'][$relatedModelName] = array();
				}
			}
		}

		// Adding document aliases to the contain key
		foreach($this->allowedAssociations as $association) {
			foreach($model->{$association} as $modelName => $modelOptions) {
				if (!in_array($modelOptions['className'], $this->documentModels)) continue;
				$query['contain'] = Set::merge($query['contain'], array($modelName => array('Metadata' => array())));
				// We also add a Version contain key for Images with Metadatas
				if ($modelOptions['className']=='Image') {
					$query['contain'][$modelName]['Version'] = array('Metadata');
				}
			}
		}

		return $query;
	}



	/**
	 *	Will modify the query to always grab documents with all the needed associated models
	 *
	 *	@param	model	$model	The model to process
	 *	@param	array	$query	The initial query
	 *	@return	boolean		True if the request should continue, false otherwise
	 **
	function beforeFind(&$model, $query) {
		// We add the version and metadata to the results
		$query = $this->__insertDocumentDataInQuery($model, $query);

		return $query;
	}


	/**
	 *	Will alter the query to add the required contained model to each document asked
	 **
	function __insertDocumentDataInQuery($model, $query = null) {
		// No query, no need to add Documents
		if (empty($query)) return true;
		//	Contains explicitly set to false, we won't add anything either
		if (!empty($query['contain']) && $query['contain']===false) return true;
		// If recursivness is negative, we will stop too
		if (!empty($query['recursive']) && $query['recursive']<0) return true;

		// We will now add associations to the request to grab associated data
		if (empty($query['contain'])) $query['contain'] = array();

		// We normalize the contain array where each key is an alias and each value its options or an empty array
		$query['contain'] = $this->__normalize($query['contain']);

		// We get the full list of linked models
		$defaultContain = array();
		foreach($model->__associations as $association) {
			foreach($model->{$association} as $assocName => &$assocValue) {
				$defaultContain[$assocName] = array();
			}
		}
		// We merge the defined contain with the binded models
		$query['contain'] = Set::merge($defaultContain, $query['contain']);
		// If contain is empty, we can stop now
		if (empty($query['contain'])) return $query;


		// For each model being an alias of a document, we will add Metadata and Versions+Metadata
		$tmpContain = $query['contain'];
		foreach($tmpContain as $key => $value) {
			// If that alias is not an alias of a Document, we skip it
			if ($model->{$key}->name!='Document') continue;
			// We will add a contain subkey
			$query['contain'][$key] = Set::merge($this->contain, $value);
		}

		return $query;
	}







	/**
	 *	Normalize a contain array.
	 *	Returned array will contain only string keys representing model names and array values (sometime empty) representing
	 *	model options.
	 *	Will act recursively
	 **
	function __normalize($array) {
		foreach($array as $key => $value) {
			// Converting numeric keys to empty array
			if (is_numeric($key) && is_string($value)) {
				$array[$value] = array();
				unset($array[$key]);
				continue;
			}
			// Skipping common keys
			if (in_array($key, array('fields', 'order', 'conditions'))) continue;

			// Recursively normalize array keys
			if (is_array($value)) {
				$array[$key] = $this->__normalize($value);
				continue;
			}
		}
		return $array;
	}
	 */






}
