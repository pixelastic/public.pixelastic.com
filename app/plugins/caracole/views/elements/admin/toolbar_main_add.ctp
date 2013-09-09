<?php
	/**
	 *	toolbar_main_add.ctp
	 *	Adding a button link to add a new item
	 **/
	// Default options
	$options = Set::merge(array(
		'url' 	=> array('action' => 'add'),
		'icon'  => Inflector::singularize($this->name).'_add',
		'class' => 'button'
		), $options
	);
	// Removing label and url
	$label = $options['label'];
	unset($options['label']);
	$url = $options['url'];
	unset($options['url']);

	echo $this->Html->tag('li',	$this->Fastcode->link($label, $url, $options));
