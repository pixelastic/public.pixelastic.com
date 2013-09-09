<?php
/**
 *	Antispam
 *	Will add measures to every form of the controller it is applied to.
 *
 *	Will add a spam bait field, named email. This field should always be kept blank, if it is filled, we guess
 *	it is a spam attack and will invalidate the form.
 *
 *	The behavior is automatically disabled on the admin panel
 **/
class AntispamComponent extends Object {
	var $isDisabled = false;

	/**
	 *	initialize
	 *	Fired before the controller beforeFilter
	 *	Used to register settings and initialize validation rules
	 **/
	function initialize(&$controller, $settings = array()) {
		// Disabling if no model set (when accessing a not-yet-defined method)
		if (empty($controller->model->alias)) {
			$this->isDisabled = true;
			return false;
		}

		// Disabling on admin
		if (!empty($controller->params['prefix']) && $controller->params['prefix']=='admin') {
			$this->isDisabled = true;
			return false;
		}

		// Shortcuts and default settings
		$this->settings = Set::merge(array(
			'model' => array($controller->model->alias),
			'emailField' => Configure::read('Antispam.emailField')
		), $settings);

		// Adding a validation rule on the spam bait
		$controller->model->validate['spambait'] = array(
			'blank' => array(
				'rule' => 'blank',
				'message' => __d('caracole_antispam', "You have to keep this field empty. If you fill it, I guess that you are a spam bot.", true)
			)
		);
	}

	/**
	 *	startup
	 *	Fired right before the controller action, but after the beforeFilter and after the Security component.
	 *	As we are altering the content of the controller posted data, if we do this before the Security Component, we will
	 *	be black holed.
	 *
	 *	If the current model's controller already have a field named "email", we should use another field
	 *	to pass this value. This is where the "emailField" key of the settings comes into play.
	 *	Just add a field in your form named as the emailField value and the component will take care of correctly
	 *	re-assign it
	 *
	 *	Sometime you will display a form related to an other model that the current model. In this case, you have to
	 *	include the model alias name in the 'model' keys of the settings
	 **/
	function startup(&$controller, $settings = array()) {
		// Disabling on admin
		if ($this->isDisabled) return false;

		// Moving data fields to correct fields
		if (!empty($controller->data)) {

			foreach($this->settings['model'] as $modelAlias) {
				// No data submitted for the model
				if (!array_key_exists($modelAlias, $controller->data)) continue;

				// Moving spambait to antispam field
				if (!empty($controller->data[$modelAlias]['email'])) {
					$controller->data[$modelAlias]['spambait'] = $controller->data[$modelAlias]['email'];
					unset($controller->data[$modelAlias]['email']);
				}

				// Moving the real email field to the email field
				$emailField = $this->settings['emailField'];
				if (!empty($emailField) && !empty($controller->data[$modelAlias][$emailField])) {
					$controller->data[$modelAlias]['email'] = $controller->data[$modelAlias][$emailField];
					unset($controller->data[$modelAlias][$emailField]);
				}

				// Calculating the posting delay
				if (!empty($controller->data[$modelAlias]['spam_timestamp'])) {
					$controller->data[$modelAlias]['spam_delay'] = mktime() - $controller->data[$modelAlias]['spam_timestamp'];
					unset($controller->data[$modelAlias]['spam_timestamp']);
				}
			}

		}

	}

	/**
	 *	beforeRender
	 *	Fired right before rendering the page.
	 *	We will move the data keys around to pre-fill the correct fields. If there are validation errors, we will
	 *	correctly re-assign them too
	 **/
	function beforeRender(&$controller) {
		// Disabling on admin
		if ($this->isDisabled) return false;

		// Moving data fields to correct fields
		if (!empty($controller->data)) {
			foreach($this->settings['model'] as $modelAlias) {
				// No data submitted for the model
				if (!array_key_exists($modelAlias, $controller->data)) continue;

				// Moving the email to the real email field
				$emailField = $this->settings['emailField'];
				if (!empty($emailField) && !empty($controller->data[$modelAlias]['email'])) {
					$controller->data[$modelAlias][$emailField] = $controller->data[$modelAlias]['email'];
					unset($controller->data[$modelAlias]['email']);
				}
				// Moving spambait to fake email field
				if (!empty($controller->data[$modelAlias]['spambait'])) {
					$controller->data[$modelAlias]['email'] = $controller->data[$modelAlias]['spambait'];
					unset($controller->data[$modelAlias]['spambait']);
				}
			}
		}

		// Moving validation errors to correct fields
		foreach($this->settings['model'] as $modelAlias) {
			// We have to find a reference to the model, either directly in the controller or related
			if ($controller->model->alias==$modelAlias) {
				$model = $controller->model;
			} elseif (!empty($controller->model->$modelAlias)) {
				$model = $controller->model->$modelAlias;
			} else {
				continue;
			}
			// Stopping if no errors
			if (empty($model->validationErrors)) continue;

			// Moving the email to the real email field
			$emailField = $this->settings['emailField'];
			if (!empty($emailField) && !empty($model->validationErrors['email'])) {
				$model->validationErrors[$emailField] = $model->validationErrors['email'];
				unset($model->validationErrors['email']);
			}
			// Moving antispam to spam bait
			if (!empty($model->validationErrors['spambait'])) {
				$model->validationErrors['email'] = $model->validationErrors['spambait'];
				unset($model->validationErrors['spambait']);
			}
		}

	}


	/**
	 *	isSpam
	 *	Will guess if the submitted data is spam or not
	 **/
	function isSpam($data = array()) {
		return false;
	}



}
