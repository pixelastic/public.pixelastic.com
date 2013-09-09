<?php
	/**
	 *	Tag view
	 *	Will display all the posts associated with a given tag
	 **/
	// Adding rss feed to head
	$this->Fastcode->rss(array(
		sprintf(__d('caracole_blog', '%1$s - Posts tagged with "%2$s"', true), Configure::read('Site.name'), $item['Tag']['name']) => array('tagSlug' => $this->params['tagSlug'], 'ext' => 'rss')
	));

	// Setting the title
	$pageTitle = sprintf(__d('caracole_blog', 'Posts tagged with "%1$s"', true), $item['Tag']['name']);

	// We will use the same view as the index
	echo $this->element('../posts/index', array(
		'plugin' => 'caracole_blog',
		'pageTitle' => $pageTitle,
		'itemList' => $itemList,
		'paginatorOptions' => array(
			'model' => 'Post',
			'url' => array(
				'tagSlug' => $this->params['tagSlug']
			)
		)
	));

	$this->set(array(
		'title_for_layout' => $pageTitle
	));
