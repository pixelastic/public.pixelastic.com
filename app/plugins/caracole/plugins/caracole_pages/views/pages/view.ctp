<?php
	/**
	 *	Default view template
	 *
	 **/

	// 404 error if no matching page
	if (empty($item)) {
		return $this->cakeError('error404');
	}

	// Setting title and description
	$this->set(array(
		'title_for_layout' => $item['Page']['name'],
		'pageCssClass' => 'page'.ucfirst($item['Page']['slug']),
		'metaDescription' => $this->Fastcode->truncate($this->Fastcode->text($item['Page']['text']))
	));


	echo $this->Fastcode->prepareHTML($item['Page']['text']);
