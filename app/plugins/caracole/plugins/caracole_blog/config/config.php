<?php
/**
 *	Blog default settings
 **/
CaracoleConfigure::write(array(
	/**
	 *	CSS and JS files
	 **/
	'Packer' => array(
		'cssAdmin' => array(
			'CaracoleBlog.admin/style',
		),
		'jsDefault' => array(
			'CaracoleBlog.init-comment'	// Posting and preview of a post
		)
	),
	/**
	 *	Blog settings
	 **/
	'Blog' => array(
		// The default blog title
		'title' => __d('caracole_blog', 'Blog', true),
		// The default meta description used to describe the blog
		'description' => __d('caracole_blog', 'Blog', true),
	)
));
