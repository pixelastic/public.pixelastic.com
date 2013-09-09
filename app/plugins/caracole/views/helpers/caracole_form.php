<?php
/**
 *	CaracoleForm
 *	This helper extends the cake FormHelper but provides easier and more meaningful arguments. Also allow for a much
 *	better handling of special form elements
 **/
class CaracoleFormHelper extends AppHelper {
	// Helpers
	var $helpers = array(
		'Form',
		'Html',
		'Caracole.Fastcode',
		'CaracoleDocuments.Document',
		'CaracoleDocuments.Image'
	);

	// Tabindex start value
	var $tabindex = 1;


	/**
	 *	button
	 *	Displaying a button
	 **/
	function button($label, $options = array()) {
		// Default values
		$options = array_merge(array(
			'tabindex' =>$this->tabindex(),
			'type' => 'button'
		), $options);

		// Prepending the icon
		if (!empty($options['icon'])) {
			$label = $this->Fastcode->icon($options['icon']).$label;
			unset($options['icon']);
		}

		return $this->Html->tag('button', $label, $options);
	}

	/**
	 *	input
	 *	Main method of this helper. Very versatile, can accept an important range of arguments and will cleverly display
	 *	the corresponding input
	 *
	 *	@param	string	$fieldName		Name of the field in field or model.field syntax
	 *	@param	mixed	$options		Array of options to pass to the FormHelper. If a string, used as the label.
	 **/
	function input($fieldName, $options = array()) {
		$this->setEntity($fieldName);

		// Using string option as the label
		if (is_string($options)) {
			$options = array('label' => $options);
		}
		// Using fieldname as default label
		if (!isset($options['label'])) {
			if (strpos($fieldName, '.')) list(,$label) = explode('.', $fieldName);
			else $label = $fieldName;
			$options['label'] = Inflector::camelize($label);
		}

		// Guessing the type
		if (empty($options['type'])) {
			$options['type'] = $this->__guessInputType($fieldName, $options);
		}

		// Default options
		$options = Set::merge(array(
			'after' => '',
			'div' => array(
				'class' => trim(implode(' ', array('input', $options['type']))),
			),
			'tabindex' => $this->tabindex()
		), $options);

		// Using a special hidden method to allow for not locking fields when validating
		if ($options['type']=='hidden') {
			unset($options['after'], $options['div'], $options['tabindex'], $options['type'], $options['label']);
			return $this->hidden($fieldName, $options);
		}

		// Delegating to DocumentHelper to display document inputs
		if ($options['type']=='document') {
			return $this->Document->input($fieldName, $options);
		}
		// Same for images
		if ($options['type']=='image') {
			$options['div']['class'] = 'input document image';
			return $this->Image->input($fieldName, $options);
		}

		// Removing the impossible-to-style fieldset + legend combo and replacing with divs
		if ($options['type']=='radio' && !empty($options['options'])) {
			$options = Set::merge($options, array(
				'legend' => false,
				'before' => $this->Fastcode->div($options['label'], array('class' => 'label')).'<div class="radios" id="'.$this->domId().'">',
				'after' => '</div>'
			));
		}

		// Allowing for multiple checkboxes
		if ($options['type']=='checkboxes' && !empty($options['options'])) {
			// We create the list of checkboxes
			$checkboxes = array();
			$modelName = $this->model();
			$checkboxFieldName = $modelName.'.'.$fieldName;
			$i = 0;
			foreach($options['options'] as $value => $label) {
				// Numeric keys are used for both label and value
				if (is_numeric($value)) $value = $label;
				$checkboxes[] = $this->Fastcode->input($checkboxFieldName, array(
					'type' => 'checkbox',
					'name' => 'data['.$modelName.']['.$fieldName.'][]',
					'id' => $this->domId().$i++,
					'value' => $value,
					'label' => $label,
					'div' => false
				));
			}
			$options = Set::merge($options, array(
				'type' => 'text',
				'class' => 'checkboxText',
				'label' => false,
				'before' => '<div class="label">'.$options['label'].'</div>',
				'between' => '<div class="checkboxes">'.implode($checkboxes).'</div>',
				'options' => null,
				'tabindex' => null,
			));
			// Adding classes and id to the container
			$options['div']['class'] = 'input checkbox checkboxMultiple';
			$options['div']['id'] = $this->domId().'Original';
			// Changing the fieldName for the "useless" input
			$fieldName = $fieldName.'_original';
		}

		// Adding tinyMCE to editor fields
		if ($options['type']=='editor') {
			$options = Set::merge($options, array(
				'type' => 'textarea',
				'class' => 'editor'
			));
		}

		// Displaying date fields in one simple input field
		if ($options['type']=='date') {
			$options = Set::merge($options, array(
				'type' => 'text',
				'maxlength' => 10,
				'length' => 10
			));
			//$fieldName.='Date';
		}

		// Displaying datetime fields in two input fields
		if ($options['type']=='datetime') {
			$options = Set::merge($options, array(
				'type' => 'text',
				'between' => '<div class="dateFull">',
				'after' => '</div>
					<div class="dateSplit">'
						.$this->input($fieldName.'Date', array('label' => false, 'div' => false, 'class' => 'date'))
						.__d('caracole', 'at', true)
						.$this->input($fieldName.'Time', array('label' => false, 'div' => false, 'class' => 'time'))
					.'</div>',
				'class' => 'full'
			));
			//$fieldName.='Date';
		}

		// Setting HABTM field as a multiple select
		if ($options['type']=='habtm') {
			$options = Set::merge($options, array(
				'type' => 'select',
				'multiple' => true,
				'class' => Inflector::tableize($this->model())
			));
		}

		// Adding an empty element to belongsTo elements
		if ($options['type']=='belongsTo') {
			$options = Set::merge($options, array(
				'type' => 'select',
				'div' => array(
					'class' => 'input select belongsTo'
				),
				'empty' => sprintf('-- %1$s --', $options['label'])
			));
		}

		// Plain text (hiding the input text and replacing by the value)
		if (!empty($options['plain'])) {
			// Finding the human-readable value
			$value = $this->__guessPlainValue($fieldName, $options);
			// Setting options
			$options = Set::merge($options, array(
				'type' => 'text',
				'style' => 'display:none',
				'after' => '<div class="plainValue">'.$value.'</div>'.$options['after']
			));
			// Adding a class
			$options['div']['class'].= ' plain';
			unset($options['plain'], $options['options']);
		}

		// Field is required
		if (!empty($options['required'])) {
			// Adding an icon
			$options['after'] = $this->Fastcode->icon('required').$options['after'];
			$options['div']['class'].=' required';
			unset($options['required']);
		}

		// Field has a title tooltip
		if (!empty($options['help'])) {
			// Adding an icon with the help text
			$options['after'].= $this->Html->tag(
				'div',
				$this->Fastcode->icon('tooltip').$this->Html->tag('span', $options['help'], array('class' => 'tooltip')),
				array('class' => 'help')
			);
			unset($options['help']);
		}
		return $this->Form->input($fieldName, $options)."\n";
	}

	/**
	 *	hidden
	 *	Overwrite of the FormHelper hidden method to allow for not locking the field when using the Security Component
	 *	We add a new argument of secureValue (default to true), if set to false, hidden field values can be changed
	 *	Thanks to http://farrworks.com/2010/02/understanding-and-escaping-the-dreaded-security-component-blackhole/
	 **/
	function hidden($fieldName, $options = array()) {
		$secure = $secureValue = true;

		if (isset($options['secure'])) {
			$secure = $options['secure'];
			unset($options['secure']);
		}

		if (isset($options['secureValue'])) {
			$secureValue = $options['secureValue'];
			unset($options['secureValue']);
		}

		$options = $this->Form->_initInputField($fieldName, array_merge(
			$options, array('secure' => false)
		));
		$model = $this->model();

		if ($fieldName !== '_method' && $model !== '_Token' && $secure && $secureValue) {
			$this->Form->__secure(null, '' . $options['value']);
		} elseif ($fieldName !== '_method' && $model !== '_Token' && $secure && !$secureValue) {
			$this->Form->__secure();
		}

		return sprintf(
			$this->Html->tags['hidden'],
			$options['name'],
			$this->Form->_parseAttributes($options, array('name', 'class'), '', ' ')
		);
	}

	/**
	 *	tabindex
	 *	Keeps track of the tabindex value inside a form. Everytime the method is fired, the tabindex value will be
	 *	incremented
	 *
	 *	@param int	$n	Will add this number to the actual tabindex value (Can be used to create a big gap between elements)
	 *	@return	int	Actual tabindex value
	 **/
	function tabindex($n = 0) {
		return (++$this->tabindexValue) + $n;
	}

	/**
	 *	validationErrors
	 *	Display a form validation errors. It will list all the errors, and add links to directly go to the corresponding
	 *	input
	 **/
	function validationErrors($validationErrors) {
		// The error title
		$title = $this->Html->tag('h3', $this->Fastcode->icon('error').__d('caracole', "It seems that we're having trouble with your form :", true));
		// Getting the error list
		$errorList = array();
		$validationErrors = array_unique(Set::flatten($validationErrors));
		foreach($validationErrors as $name => $error) {
			list($modelName, $fieldName) = explode('.', $name);
			// Creating a <li> with a link to the field
			$errorList[] = $this->Html->tag(
				'li',
				$this->Fastcode->link(
					$error,
					'#'.$modelName.Inflector::camelize($fieldName)
				)
			);
		}
		// The final message
		return $title.$this->Html->tag('ul', implode("\n", $errorList));
	}


	/**
	 *	__guessInputType
	 *	Guess the input type based on its name and set of options
	 *
	 *	@param	string	$fieldName		Name of the field in field or model.field syntax
	 *	@param	mixed	$options		Array of options to pass to the FormHelper. If a string, used as the label.
	 **/
	function __guessInputType($fieldName, $options = array()) {
		// A type is defined, we use this one
		if (!empty($options['type'])) {
			return $options['type'];
		}

		// Getting model and field keys
		if (strpos($fieldName, '.')) {
			list($modelKey, $fieldKey) = explode('.', $fieldName);
		} else {
			$modelKey = $this->model();
			$fieldKey = $fieldName;
		}
		// Introspecting model if not already set
		if (!isset($this->Form->fieldset[$modelKey])) {
			$this->Form->_introspectModel($modelKey);
		}
		// Getting primary key
		$primaryKey = !empty($this->Form->fieldset[$modelKey]) ? $this->Form->fieldset[$modelKey]['key'] : null;

		// Primary key (hidden)
		if ($fieldKey==$primaryKey) {
			return 'hidden';
		}
		// Default types
		if (!empty($this->Form->fieldset[$modelKey]['fields'][$fieldName])) {
			$defaultType = $this->Form->fieldset[$modelKey]['fields'][$fieldName]['type'];
			if ($defaultType=='datetime') return 'datetime';
		}

		// HABTM
		if ($modelKey==$fieldKey) {
			return 'habtm';
		}

		// Option passed, it should be a select
		if (isset($options['options'])) {
			return 'select';
		}
		// Password
		if (in_array($fieldKey, array('pass', 'psword', 'pwd', 'passwd', 'password'))) {
			return 'password';
		}
		// Text field
		if (in_array($fieldKey, array('text', 'content'))) {
			return 'editor';
		}
		//	Document field
		if (preg_match('/^document_/', $fieldKey)) {
			return 'document';
		}
		//	Image field
		if (preg_match('/^image_/', $fieldKey)) {
			return 'image';
		}
		//	Unique checkbox (Yes / No)
		if (preg_match('/^is_/', $fieldKey)) {
			return 'checkbox';
		}
		// Belongs to (select)
		if (preg_match('/_id$/', $fieldKey)) {
			return 'belongsTo';
		}
		// Nothing found, we keep cake default guessing
		return null;
	}

	/**
	 *	__guessPlainValue
	 *	Will try to guess the plain value of the actual field. Plain value is not the exact same value as the one saved in the field,
	 *	it is a more human-readable form of it
	 **/
	function __guessPlainValue($fieldName, &$options = array()) {
		// If one is defined, we take it, otherwise we revert to the form method
		$value = empty($options['value']) ? array_pop($this->Form->value()) : $options['value'];
		unset($options['value']);

		// We will make it more human readable for some edge cases
		switch($options['type']) {
			// Yes / No checkbox
			case 'checkbox':
				$value = ($value==1) ? __d('caracole', 'Yes', true) : __d('caracole', 'No', true);
			break;
			// Select
			case 'select' :
				if (array_key_exists($value, $options['options'])) $value = $options['options'][$value];
			break;
		}

		return $value;

	}




}
