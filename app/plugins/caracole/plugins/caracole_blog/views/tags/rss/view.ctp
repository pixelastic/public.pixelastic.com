<?php
	/**
	 *	Rss blog search
	 *	Will return a feed of only posts with specified tag
	 **/

	// We use the default view
	echo $this->element('../posts/rss/index', array('plugin' => 'caracole_blog'));

	// Setting title and description
	$this->set(array(
		'title_for_layout' => sprintf(__d('caracole_blog', '%1$s - Posts tagged with "%2$s"', true), Configure::read('Site.name'), $item['Tag']['name']),
	));
