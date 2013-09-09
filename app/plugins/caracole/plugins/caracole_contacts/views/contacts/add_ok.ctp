<?php

	/**
	 *	Form correctly sent
	 **/
	$this->set(array(
		'pageCssId' => 'contact',
		'title_for_layout' => __d('caracole_contacts', 'Thank you', true)
	));


	echo $this->Fastcode->message(
		__d('caracole_contacts', "Thank you for your message, we'll come back to you as fast as possible.", true),
		"success"
	);