<?php
	/**
	 *	Post view
	 *	Will display only one blog post
	 **/
	// Setting title and description
	$this->set(array(
		'pageCssId' => 'blog',
		'title_for_layout' => $item['Post']['name'],
		'metaDescription' => $this->Fastcode->truncate($this->Fastcode->text($item['Post']['text']))
	));
?>

<!-- Blog layout -->
<div class="blogLayout">

	<!-- Blog main -->
	<div class="blogMain span-16">
		<?php
			// Setting a title. We do not escape to allow html markup in blog post
			echo $this->Html->tag('h2', $this->Fastcode->link($item['Post']['name'], $this->Fastcode->url()));

			// Post
			echo $this->element('../posts/display', array(
				'plugin' => 'caracole_blog',
				'hideTitle' => true,
				'item' => $item
			));

			// Comments
			echo $this->element('../comments/index', array(
				'plugin' => 'caracole_blog',
				'itemList' => $item['Comment'],
				'spamCount' => $item['Post']['spam_count'],
				'post_id' => $item['Post']['id']
			));
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