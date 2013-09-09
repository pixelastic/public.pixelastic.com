<?php
/**
 *	You can here define, for each model, translation for the most common used strings in the admin panel.
 *	It is mostly used so your translation file can correctly handle irregular plural form and verbs.
 *
 *	If you don't define strings for your own models, default strings will be used based on the model
 *	inner $name property.
 **/
CaracoleConfigure::write(array(
	'I18n' => array(
		'PixelasticContact' => array(
			'human' 		=> __('Contact', true),
			'plural' 		=> __('contacts', true),
			'add'			=> __('New contact', true),
			'edit'			=> __('Edit contact', true),
			'added'			=> __('Contact "%1$s" added', true),
			'edited'		=> __('Contact "%1$s" edited', true),
			'deleted'		=> __('Contact "%1$s" deleted', true),
			'restored'		=> __('Contact "%1$s" restored', true),
			'destroyed'		=> __('Contact "%1$s" destroyed', true),
			'reordered'		=> __('Contacts reordered', true),
		)
		/*
		 'ModelName' => array(
			'human' 		=> __('modelName', true),
			'plural' 		=> __('modelNames', true),
			'add'			=> __('New modelName', true),
			'edit'			=> __('Edit modelName', true),
			'added'			=> __('modelName "%1$s" added', true),
			'edited'		=> __('modelName "%1$s" edited', true),
			'deleted'		=> __('modelName "%1$s" deleted', true),
			'restored'		=> __('modelName "%1$s" restored', true),
			'destroyed'		=> __('modelName "%1$s" destroyed', true),
			'reordered'		=> __('modelNames reordered', true),
		)
		*/
	)
));
