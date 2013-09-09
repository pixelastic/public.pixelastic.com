<?php

	/**
	 *	Add/Edit form
	 *	Allow admin adding/editing of new items in the database
	 **/

	// Special page css
	$this->set('pageCssId', 'adminEdit');

	// Validation errors
	if (!empty($this->validationErrors)) {
		echo $this->Fastcode->message($this->Fastcode->validationErrors($this->validationErrors), 'error');
	}

	// We parse the url to find the controller and action
	$pageParams = Router::parse($this->here);

	// Starting the form (with upload capability)
	echo $this->Form->create(null, array(
		'url' => $this->here,
		'enctype' 	=> 'multipart/form-data',
		'class'		=> 'niceForm editForm',
		'id' => Inflector::classify($this->params['controller']).Inflector::camelize($this->params['action']).'Form'
	));

	// Upload max file size
	echo $this->Html->tag('input', null, array(
		'type' => 'hidden',
		'name' => 'MAX_FILE_SIZE',
		'id' => 'MAX_FILE_SIZE',
		'value' => CaracoleNumber::toMachineSize(ini_get('upload_max_filesize'))
	));

	// Adding the hidden id field
	echo $this->Form->hidden('id');

	// We will order fields in their corresponding tabs (general or advanced)
	$tabs = array('general' => array(), 'advanced' => array());
	foreach($fields as $fieldName => &$fieldOptions) {
		// If the advanced option is set, we put it in the advanced tab
		if (is_array($fieldOptions) && !empty($fieldOptions['advanced'])) {
			unset($fieldOptions['advanced']);
			$tabs['advanced'][] = array('name' => $fieldName, 'options' => $fieldOptions);
		} else {
			$tabs['general'][] = array('name' => $fieldName, 'options' => $fieldOptions);
		}
	}

	// Each fieldset will work as a tab, so we need to wrap them
	echo $this->Fastcode->div(null, array('class' => 'fieldsets'));

		// Title
		echo $this->Html->tag('h3', $title);

		// Displaying the tabs list
		if (!empty($tabs['advanced'])) {
			echo $this->Html->tag('ul', null, array('class' => 'tabMenu'));
				// Displaying each tab
				foreach($tabs as $tabName => &$inputs) {
					echo $this->Html->tag('li',
						$this->Fastcode->link(
							($tabName=='general') ? __d('caracole', 'General', true) : __d('caracole', 'Advanced', true),
							'#fieldset'.ucfirst($tabName)
						)
					);
				}
			echo '</ul>';
		}

		// Displaying each fieldset with its inner inputs
		foreach($tabs as $tabName => &$inputs) {
			// Skipping empty fieldsets
			if (empty($inputs)) continue;

			// Starting the fieldset
			echo $this->Html->tag('fieldset', null, array('id' => 'fieldset'.ucfirst($tabName), 'class' => 'tabPanel'));
			echo $this->Html->tag('legend', ($tabName=='general') ? __d('caracole', 'General', true) : __d('caracole', 'Advanced', true));

			// Displaying each input
			foreach($inputs as $input) {
				echo $this->Fastcode->input($input['name'], $input['options']);
			}

			// Closing the fieldset
			echo '</fieldset>';
		}


	// Closing the wrapping div
	echo '</div>';

	// We allow for an option to hide the submit button
	if (empty($hideSubmit) || !$hideSubmit) {
		// Adding a submit button
		echo $this->Html->div('submit',
				$this->Fastcode->button(
				($pageParams['action']=='add') ? __d('caracole', 'Add', true) : __d('caracole', 'Edit', true),
				array(
					'icon' => 'valid',
					'type' => 'submit'
				)
			)
		);
	}


	// Ending the form
	echo $this->Form->end();
