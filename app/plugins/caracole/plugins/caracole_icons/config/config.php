<?php
/**
 *	Icon default settings
 **/
CaracoleConfigure::write(array(
	/**
	 *	CSS and JS files
	 **/
	'Packer' => array(
		'cssDefault' => array(
			'CaracoleIcons.style',		//	Default icon definitions
			'icons'						//	Generated icon rules
		),
		'cssAdmin' => array(
			'CaracoleIcons.admin/style',			//	Styling of the icons list
		)
	),
	/**
	 *	Icons path and size configuration
	 **/
	'Icons' => array(
		'cell' => array(
			'width' => 22,
			'height' => 22
		)
	)
));
