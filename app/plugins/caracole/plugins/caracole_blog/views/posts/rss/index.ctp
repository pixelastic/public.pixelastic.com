<?php
	/**
	 *	Rss blog index
	 **/
	// Setting title and description
	$this->set(array(
		'title_for_layout' => sprintf(__d('caracole_blog', '%1$s - Blog', true), Configure::read('Site.name')),
		'metaDescription' => $this->Fastcode->truncate($this->Fastcode->text(Configure::read('Blog.description')))
	));

	// Will convert the itemList in an array of items to display
	$originalItemList = $itemList;
	$itemList = array();
	foreach($originalItemList as &$item) {
		// Url
		$url = $this->Html->url(Post::url($item), true);
		// Item
		$itemList[] = array(
			'title' => $item['Post']['name'],
			'link' => $url,
			'guid' => array('url' => $url, 'isPermalink' => 'true'),
			'description' => $item['Post']['text'],
			'pubDate' => $item['Post']['publish_start']
		);
	}
	$this->set('itemList', $itemList);
