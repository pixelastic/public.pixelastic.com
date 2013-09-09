<?php
	$this->set(array(
		'title_for_layout' => __d('caracole_users', 'I forgot my password (Step 1/2)', true),
		'pageCssClass' => 'logoutLayout userPassSent'
	));

	echo $this->Fastcode->message(
		__d('caracole_users', "You should have received an email with all the needed informations to regenerate your password.", true),
		'success',
		array('class' => 'passSentInfo')
	);

	// Back to login form
	echo $this->Fastcode->link(
		__d('caracole_users', "Back", true),
		array('action' => 'login'),
		array('class' => 'back')
	);
