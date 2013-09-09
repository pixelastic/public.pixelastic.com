<?php
	/**
	 *	Archive
	 *	Displays posts of a given year / month
	 **/

	// Setting the title
	if (empty($this->params['month'])) {
		$pageTitle = sprintf(__d('caracole_blog', 'Posts from %1$s', true), $this->params['year']);
	} else {
		$pageTitle = sprintf(
			__d('caracole_blog', 'Posts from %2$s %1$s', true),
			$this->params['year'],
			CaracoleI18n::strftime("%B", strtotime('2000-'.$this->params['month'].'-01 00:00:00'))
		);
	}

	// We will use the same view as the index
	echo $this->element('../posts/index', array(
		'plugin' => 'caracole_blog',
		'pageTitle' => $pageTitle,
		'itemList' => $itemList
	));

	$this->set(array(
		'title_for_layout' => $pageTitle
	));



?>
