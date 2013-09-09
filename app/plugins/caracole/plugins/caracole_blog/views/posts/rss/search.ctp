<?php
	/**
	 *	Rss blog search
	 *	Will return a feed of only posts matching the search query
	 **/

	// We use the default view
	echo $this->element('../posts/rss/index', array('plugin' => 'caracole_blog'));

	// Setting title and description
	$this->set(array(
		'title_for_layout' => sprintf(__d('caracole_blog', '%1$s - Posts matching "%2$s"', true), Configure::read('Site.name'), $keyword),
	));
