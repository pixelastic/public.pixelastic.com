<?php
	/**
	 *	contact form
	 **/
	$this->set(array(
		'pageCssId' => 'contact',
		'title_for_layout' => __d('caracole_contacts', 'Contact', true)
	));

	echo $this->Form->create('Contact', array(
		'url' => $this->Fastcode->url(array('action' => 'add')),
		'class'		=> 'contactForm niceForm',
		'id' => 'ContactAddForm'
	));

	// Flash message
	if (!empty($this->validationErrors)) {
		echo $this->Fastcode->message($this->Fastcode->validationErrors($this->validationErrors), 'error');
	}

	// Name
	echo $this->Fastcode->input('name', array(
		'label' => __d('caracole_contacts', 'Name', true),
		'required' => true
	));

	// Spam bait
	echo $this->Antispam->input();

	// Real email field
	echo $this->Fastcode->input('calirhoe', array(
		'label' => __d('caracole_contacts', 'Email', true),
		'help' => __d('caracole_contacts', 'Will not be published, but needed to contact you back.', true),
		'required' => true
	));

	// Text
	echo $this->Fastcode->input('text', array(
		'label' => __d('caracole_contacts', 'Message', true),
		'type' => 'textarea'
	));

	// Submit
	echo $this->Fastcode->div(
		$this->Fastcode->button(
			__d('caracole_contacts', 'Send', true),
			array(
				'type' => 'submit'
			)
		),
		array('class' => "submit")
	);

	// Ending the form
	echo $this->Form->end();