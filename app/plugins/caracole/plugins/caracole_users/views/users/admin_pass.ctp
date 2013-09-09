<?php
	$this->set(array(
		'title_for_layout' => __d('caracole_users', 'I forgot my password (Step 1/2)', true),
		'pageCssClass' => 'logoutLayout userPass'
	));

	// Form options
	if (empty($formOptions)) $formOptions = array();
	$formOptions = Set::merge(array(
		'url' => $this->here,
		'id' => 'regeneratePassForm',
		'class'	=> 'loginForm niceForm'
	), $formOptions);

	// Validation errors or message
	if (!empty($this->validationErrors)) {
		echo $this->Fastcode->message($this->Fastcode->validationErrors($this->validationErrors), 'error');
	} else {
		echo $this->Fastcode->message(
			__d('caracole_users', "You forgot your password. Don't worry, you just have to type your email and we will send you instructions on how to regenerate it.", true),
			'information',
			array('class' => 'passInfoText')
		);
	}

	// Starting the form
	echo $this->Form->create(null, $formOptions);

	// Email
	echo $this->Fastcode->input('name', array(
		'label' => __d('caracole_users', 'Email', true),
		'required' => true
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
			__d('caracole_users', 'Regenerate my password', true),
			array(
				'icon' => 'valid',
				'type' => 'submit'
			)
		);

	 echo '</div>';

	// Ending the form
	echo $this->Form->end();
