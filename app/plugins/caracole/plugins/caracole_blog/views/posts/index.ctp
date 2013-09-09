<?php
	/**
	 *	Blog index.
	 *	Will present the list of blog posts
	 **/
	// Setting title and description
	$this->set(array(
		'pageCssId' => 'blog',
		'title_for_layout' => Configure::read('Blog.title'),
		'metaDescription' => Configure::read('Blog.description')
	));

	// Setting default options
	if (empty($paginatorOptions)) $paginatorOptions = array();
	if (empty($pageTitle)) $pageTitle = __d('caracole_blog', 'Blog', true);

?>

<!-- Blog layout -->
<div class="blogLayout container">

	<!-- Blog main -->
	<div class="blogMain span-16">
		<?php
			// Setting a title
			echo $this->Html->tag('h2', $this->Fastcode->link($pageTitle, $this->here));

			// No results
			if (empty($itemList)) {
				echo $this->Fastcode->message(__d('caracole_blog', 'Sorry, no post matches your query.', true));
			} else {
				// Post list
				foreach($itemList as &$item) {
					echo $this->element('../posts/display', array(
						'plugin' => 'caracole_blog',
						'item' => $item
					));
				}

				// Paginating results
				echo $this->element('paginate', array(
					'plugin' => 'caracole',
					'paginatorOptions' => $paginatorOptions
				));
			}
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