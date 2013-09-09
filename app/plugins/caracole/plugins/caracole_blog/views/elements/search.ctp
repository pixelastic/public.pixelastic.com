<?php
	/**
	 *	Searching through the blog posts
	 **/

	echo $this->Html->tag('h3', __d('caracole_blog', 'Search', true));

	// Search form
	echo $this->Form->create('Post', array(
		'url' => array('action' => 'search'),
		'class'		=> 'searchForm'
	));

	// Search field
	echo $this->Fastcode->input('keyword', array(
		'label' => __d('caracole_blog', 'Keyword', true)
	));

	// Submit
	echo $this->Fastcode->button(
		__d('caracole_blog', 'Search', true),
		array(
			'type' => 'submit',
		)
	);

	// Going to archive
	echo $this->Fastcode->p(
		sprintf(
			__d('caracole_blog', 'or browse through the %1$s', true),
			$this->Fastcode->link(
				__d('caracole_blog', 'archive', true),
				array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'archive')
			)
		),
	'archive'
	);

	// Ending the form
	echo $this->Form->end();


?>
