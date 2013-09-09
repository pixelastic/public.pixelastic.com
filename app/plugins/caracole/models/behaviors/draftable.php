<?php
/**
 *	Draftable
 *	Allow the item to be saved as a draft. Drafted items won't be returned on default searchs.
 *	You have to add a tinyint(1) is_draft field to the model table
 *
 *	One can always disable the behavior by calling the disableDraftable or disableDraftablePermanently and calling
 *	the enableDraftable method to re-enable it.
 *
 *	TODO : This behavior only works for the current model, not for related models.
 *		If Page hasMany Comment, when fetching pages it will fetch drafted comments
 *		Also, if Page belongsTo Section, when fetching Pages, you'll also fetch drafted section
 *		I guess the second exemple can be fixed by adding conditions on the query if the model belongsTo
 *		a draftable model.
 *		But, I don't know how to add conditions on the hasMany approach
 *
 **/
class DraftableBehavior extends ModelBehavior  {
	// List of disabled models. If model in the list, it is disabled. If value set to true, it is permanently disabled
	var $__disabledModels = array();

	/**
	 *	setup
	 *	Init the behavior. Make the admin panel display the drafted items
	 **/
	function setup(&$model, $config = array()) {
		$model->adminSettings = Set::merge(
			array(
				'index' => array(
					'paginate' => array(
						$model->alias => array(
							'conditions' => array($model->alias.'.is_draft' => array(0,1)),
							'fields' => array($model->alias.'.is_draft')
						)
					)
				),
				'fields' => array(
					'is_draft' => array(
						'label' => __d('caracole', 'Save as draft', true),
						'help' => __d('caracole', 'Check this to save this item as a draft. Drafts are not displayed on the public site.', true),
						'advanced' => true
					)
				)
			),
			$model->adminSettings
		);
	}

	/**
	 *	beforeFind
	 *	Will alter every find call to not return elements marked as draft by default
	 **/
	function beforeFind(&$model, $query) {
		// Is the behavior disabled for this model ?
		if (array_key_exists($model->alias, $this->__disabledModels)) {
			// Re-enabling it if non-permanent
			if ($this->__disabledModels[$model->alias]===false) {
				$this->enableDraftable($model);
			}
			return true;
		}

		// Getting the condition to add to the draft field
		$draftValue = empty($query['conditions'][$model->alias.'.is_draft']) ? '0' : $query['conditions'][$model->alias.'.is_draft'];
		$query = Set::merge($query,  array('conditions' => array($model->alias.'.is_draft' => $draftValue)));

		return $query;
	}


	/**
	 *	beforeDelete
	 *	We will disable the draftable behavior before deleting an element, otherwise drafted elements could never ve deleted because
	 *	they could never be found in the table
	 **/
	function beforeDelete(&$model, $cascade = true) {
		$this->disableDraftable($model);
		return true;
	}

	/**
	 *	draftableDisable
	 *	Disable the behavior for the next find call. Every items will be returned, regardless of their draft status
	 *
	 *	@param	boolean		$permanent	If set to true, the model will not be re-enabled after the next find call.
	 *									Default to false
	 **/
	function disableDraftable(&$model, $permanent = false) {
		// We add the model to the list of disabled models
		$this->__disabledModels[$model->alias] = $permanent;
	}

	/**
	 *	disableDraftablePermanently
	 *	Alias for disabling the behavior permanently (for every subsequent calls, until enableDraftable is called)
	 **/
	function disableDraftablePermanently(&$model) {
		$this->disableDraftable($model, true);
	}

	/**
	 *	enableDraftable
	 *	Re-enable the behavior for the model (removing the entry from the disabledModels list)
	 **/
	function enableDraftable(&$model) {
		if (array_key_exists($model->alias, $this->__disabledModels)) {
			unset($this->__disabledModels[$model->alias]);
		}
	}



}
