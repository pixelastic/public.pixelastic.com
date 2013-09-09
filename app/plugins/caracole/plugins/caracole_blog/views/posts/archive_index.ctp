<?php
	/**
	 *	Search index
	 *	Displays a form to make a search
	 **/
	$pageTitle = __d('caracole_blog', 'Blog post archive', true);
	$this->set(array(
		'pageCssId' => 'blog',
		'title_for_layout' => $pageTitle
	));
?>
<!-- Blog layout -->
<div class="blogLayout container">

	<!-- Blog main -->
	<div class="blogMain span-16">

<?php
	// Setting the title
	echo $this->Html->tag('h2', $this->Fastcode->link($pageTitle, $this->here, array('escape' => false)));

	// Information message
	echo $this->Fastcode->message(
		__d('caracole_blog', 'Feel free to search trough the entire blog or directly jump to any given month in the archive.', true)
	);

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
			'type' => 'submit'
		)
	);

	// Ending the form
	echo $this->Form->end();

	// Displaying the calendar
	echo $this->element('calendar', array('plugin' => 'caracole_blog', 'itemList' => $itemList));

?>
</div>
	<!-- /Blog main -->

	<!-- Blog secondary -->
	<div class="blogSecondary span-7 prepend-1 last">
		<?php
			// Sidebar
			echo $this->element('sidebar', array(
				'plugin' => 'caracole_blog'
			));
		?>
	</div>
	<!-- /Blog secondary -->

</div>
<!-- /Blog layout -->