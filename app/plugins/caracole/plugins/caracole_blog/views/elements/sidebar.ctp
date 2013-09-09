<?php
	/**
	 *	Sidebar
	 **/

	// Search
	echo $this->element('search', array(
		'plugin' => 'caracole_blog'
	));

	// Rss
	echo $this->element('rss', array(
		'plugin' => 'caracole_blog'
	));

	// Recent posts
	if (!empty($recentPostList)) {
		echo $this->element('post_recent', array(
			'plugin' => 'caracole_blog',
			'itemList' => $recentPostList
		));
	}

	// Recent comments
	if (!empty($recentCommentList)) {
		echo $this->element('comment_recent', array(
			'plugin' => 'caracole_blog',
			'itemList' => $recentCommentList
		));
	}

	// Popular tags
	if (!empty($popularList)) {
		echo $this->element('popular', array(
			'plugin' => 'caracole_blog',
			'itemList' => $popularList
		));
	}


?>
