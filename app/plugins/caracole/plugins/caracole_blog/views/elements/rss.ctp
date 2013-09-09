<?php
	/**
	 *	Rss Feed
	 **/

	// We add the default rss field
	$defaultRssUrl = array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'index', 'ext' => 'rss');
	$defaultRss = array(Configure::read('CaracoleBlog.title') => $defaultRssUrl);
	$this->Fastcode->rss($defaultRss);

	// Title
	echo $this->Html->tag('h3', __d('caracole_blog', 'RSS Feed', true));

	// Subscribe link
	echo $this->Fastcode->link(
		__d('caracole_blog', 'Subscribe to RSS feed', true),
		$defaultRssUrl,
		array('class' => 'rss')
	);


?>
