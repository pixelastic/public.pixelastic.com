<?php
	/**
	 *	Default home page for a clean install
	 **/
	if (empty($item)) {
		echo sprintf(
			__d('caracole_pages', 'Welcome to Caracole. To overwrite this default page either create a new page with a slug of "%1$s", or create a %2$s file in %3$s', true),
			'home',
			'home.ctp',
			'app/views/pages/'
		);
	} else {
		echo $this->Html->tag('h2', $item['Page']['name']);

		echo $item['Page']['text'];
	}
