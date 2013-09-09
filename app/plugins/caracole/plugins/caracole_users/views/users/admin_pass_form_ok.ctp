<?php
	$this->set(array(
		'title_for_layout' => __d('caracole_users', 'I forgot my password (Step 2/2)', true),
		'pageCssClass' => 'logoutLayout userPassFormOk'
	));

	echo $this->Fastcode->message(
		__d('caracole_users', "Your password has been changed. You can now log in with your new credentials.", true),
		'success',
		array('class' => 'passChangedInfo')
	);

	// Back to login form
	echo $this->Fastcode->link(
		__d('caracole_users', "Back", true),
		array('action' => 'login'),
		array('class' => 'back')
	);
