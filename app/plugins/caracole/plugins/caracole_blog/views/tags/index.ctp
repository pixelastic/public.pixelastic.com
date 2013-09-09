<?php
	/**
	 *	Tag index
	 *	Will display all the available tags
	 **/
	$pageTitle = __d('caracole_blog', 'Tag index', true);
	$this->set(array(
		'pageCssId' => 'blog',
		'title_for_layout' => $pageTitle
	));
?>
<!-- Blog layout -->
<div class="blogLayout">

	<!-- Blog main -->
	<div class="blogMain span-16">
		<?php
			// Setting the title
			echo $this->Html->tag('h2', $this->Fastcode->link($pageTitle, $this->here));

			// Information message
			echo $this->Fastcode->message(
				__d('caracole_blog', 'Here are all the available tags, in order of importance.', true)
			);

			// We get the total number of posts tagged
			$total = 0;
			foreach($itemList as &$item) $total+=$item['Tag']['post_count'];

			// We now display a full list of all the tags
			echo $this->Html->tag('dl', null, array('class' => 'list tagList'));
			foreach($itemList as &$item) {
				// We set a percent width for each of them
				$percent = max(1, ceil($item['Tag']['post_count']*100/$total));
				echo $this->Html->tag('dt',
					$this->Fastcode->link(
						$item['Tag']['name'],
						Tag::url($item)
					)
				);
				echo $this->Html->tag('dd', $item['Tag']['post_count'], array('style' => 'width:'.$percent.'%'));
			}
			echo '</dl>';
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