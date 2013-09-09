<?php
	$this->set(array(
		'title_for_layout' => __d('caracole_users', 'I forgot my password (Step 2/2)', true),
		'pageCssClass' => 'logoutLayout userPassForm'
	));

	// Form options
	if (empty($formOptions)) $formOptions = array();
	$formOptions = Set::merge(array(
		'url' => $this->here,
		'id' => 'changePassForm',
		'class'	=> 'loginForm niceForm'
	), $formOptions);

	// Validation errors or message
	if (!empty($this->validationErrors)) {
		echo $this->Fastcode->message($this->Fastcode->validationErrors($this->validationErrors), 'error');
	} else {
		echo $this->Fastcode->message(
			__d('caracole_users', "You asked for generating a new password. Just type your new password and its confirmation in the fields below.", true),
			'information',
			array('class' => 'passInfoText')
		);
	}

	// Starting the form
	echo $this->Form->create(null, $formOptions);

	// Password
	echo $this->Fastcode->input('password', array(
		'label' => __d('caracole_users', 'Password', true),
		'autocomplete' => 'off',	// Otherwise Firefox will fill the field with pre-saved passwords
		'required' => true
	));

	// Password confirmation
	echo $this->Fastcode->input('password_confirm', array(
		'label' => __d('caracole_users', 'Confirmation', true),
		'required' => true,
		'type' => 'password',
		'autocomplete' => 'off',	// Otherwise Firefox will fill the field with pre-saved passwords
		'help' => __d('caracole_users', 'Type your password again, to avoid typos.', true)
	));

	echo '<div class="submit">';

		// Back to login form
		echo $this->Fastcode->link(
			__d('caracole_users', "Back", true),
			array('action' => 'login'),
			array('class' => 'back')
		);

		// Adding a submit button
		echo $this->Fastcode->button(
			__d('caracole_users', 'Change my password', true),
			array(
				'icon' => 'valid',
				'type' => 'submit'
			)
		);

	 echo '</div>';

	// Ending the form
	echo $this->Form->end();
