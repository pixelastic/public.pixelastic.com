<?php
	/**
	 *	Contact sidebar
	 **/
	echo $this->Html->tag('h3', __('Who am I ?', true));

	echo $this->Fastcode->message(
		sprintf(__('My name is <strong>Timothée Carry-Caignon</strong>, I\'m %1$s and living in Paris, France', true), floor((mktime()-mktime(1,1,1,5,4,1984)) / 31536000)),
		'notice'
	);

	echo $this->Html->tag('h3', __('What do I do ?', true));

	echo $this->Fastcode->message(
		__("I'm passionate about all things web-development from front-end to back-end and I build great websites for a living.", true),
		'notice'
	);

	echo $this->Html->tag('h3', __('How to contact me ?', true));

	echo $this->Fastcode->message(
		__("I have no Facebook page but sometime tweet. If you want to contact me, the easiest way is to send a mail (tim@*domain-of-this-website*.com) or use the form on this page.", true),
		'notice'
	);
