<?php
	$this->set(array(
		'title_for_layout' => __d('caracole_users', 'I forgot my password (Step 2/2)', true),
		'pageCssClass' => 'logoutLayout userPassTokenError'
	));

	echo $this->Fastcode->message(
		$this->Fastcode->p(__d('caracole_users', "It seems that the link you followed to regenerate your password expired. They do not live longer than 24 hours.", true)).
		$this->Fastcode->p(__d('caracole_users', "Feel free to start the procedure again to get a fresh new link.", true)),
		'error',
		array('class' => 'passChangedInfo')
	);

	// Back to login form
	echo $this->Fastcode->link(
		__d('caracole_users', "Back", true),
		array('action' => 'login'),
		array('class' => 'back')
	);
