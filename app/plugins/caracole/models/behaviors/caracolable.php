<?php
/**
 *	Caracolable
 *	Main behavior added to every Caracole model. It will set some default variable for each model
 **/
class CaracolableBehavior extends ModelBehavior {
	// List of models where this behavior is set up
	var $setupModels = array();


	/**
	 *	setup
	 *	Initiate the behavior. Will set default model values
	 *	Make sure the behavior is only called once per model
	 ***/
	function setup(&$model, $settings = array()) {
		// Asserting that the behavior is only called once
		if (in_array($model->alias, $this->setupModels)) return;
		// We set the admin settings
		$this->__setAdminSettings($model);
		// Marking as set up
		$this->setupModels[] = $model->alias;
	}



	/**
	 *	__setAdminSettings
	 *	Will save into the model all the admin settings (telling Caracole how this model will be displayed in the admin
	 *	panel). It handle settings like pagination filters, toolbar options and fields to display.
	 *
	 *	Each model can have its own $adminSettings value that will be merged with the default one. You can set a value to false
	 *	to remove this entry
	 **/
	function __setAdminSettings(&$model) {
		// We merge default settings with given settings
		$model->adminSettings = Set::merge(array(
			/**
			 *	Toolbar options.
			 *	Main options are displayed at the top of the page, near the title.
			 *	Secondary options are displayed in their own toolbar just after the title.
			 *	Each array is composed of keys representing the action page they should appear on
			 **/
			'toolbar' => array(
				'main' => array(
					'index' => array(
						'add' => $model->translate('add'),
						'search' => array(
							'mainField' => $model->displayField,	//	The field on which make the default search
							'autocomplete' => true					// Enabling the autocomplete feature
						)
					),
					'add' => false,
					'edit' => false
				),
				'secondary' => array(
					'index' 	=> false,
					'add' 		=> array('back'),
					'edit'		=> array('back'),
					'reorder' 	=> array('back'),
					'view'		=> array('back'),

				)
			),
			/**
			 *	Fields
			 *	Fields to display in the add/edit page
			 **/
			'fields' => array(),
			/**
			 *	Index
			 *	List all the items. We can define headers as well as pagination options
			 *
			 *	Headers : 	Each key is the field on which the reorder will be set. It also the class of the th element.
			 *				The key is the label displayed in the th element
			 *				If you want to disable the reordering on this field (maybe because it is a virtual field), you can
			 *				pass an array('order' => false, 'label' => 'Label')
			 *
			 *	Actions	:	Each key is the name of an action to be applied to checked items.
			 *				Each key represent the label that will be displayed
			 **/
			'index' => array(
				'headers' => array(
					$model->alias.'.'.$model->displayField => __d('caracole', 'Name', true)
				),
				'actions' => array(
					'delete' => __d('caracole', 'Delete', true)
				),
				 'paginate' => array(
					$model->alias => array(
						'conditions' => array(),
						'fields' => array(
							$model->alias.'.'.$model->primaryKey,
							$model->alias.'.'.$model->displayField
						),
						'order' => $model->order,
						'limit' => 25,
						'contain' => false
					)
				)
			),
			/**
			 *	Special views
			 *	Every action listed in this array will use the controller custom view instead of the default admin view
			 **/
			'views' => array(

			)
		), $model->adminSettings);

		// Making sure the fields key at least contain the displayField
		if (empty($model->adminSettings['fields'])) {
			$model->adminSeetings['fields'] = array($model->displayField => $model->displayField);
		}

		// Toolbars for search is the same as for index
		if (empty($model->adminSettings['toolbar']['main']['search']) && !empty($model->adminSettings['toolbar']['main']['index'])) {
			$model->adminSettings['toolbar']['main']['search'] = $model->adminSettings['toolbar']['main']['index'];
		}

	}

}
