<?php
	/**
	 *	Search
	 *	Returns blog posts that match the search criterias
	 **/
	// Setting the title
	$pageTitle = sprintf(__d('caracole_blog', 'Search results for "%1$s"', true), $keyword);

	// Adding rss feed to head
	$this->Fastcode->rss(array(
		sprintf(__d('caracole_blog', '%1$s - Posts matching "%2$s"', true), Configure::read('Site.name'), $keyword) => array('keyword' => $keyword, 'ext' => 'rss')
	));

	// We will use the same view as the index
	echo $this->element('../posts/index', array(
		'plugin' => 'caracole_blog',
		'pageTitle' => $pageTitle,
		'itemList' => $itemList,
		'paginatorOptions' => array(
			'url' => array(
				'keyword' => $this->params['keyword']
			)
		)
	));

	$this->set(array(
		'title_for_layout' => $pageTitle
	));

?>
