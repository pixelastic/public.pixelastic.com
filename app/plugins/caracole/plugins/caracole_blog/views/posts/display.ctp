<?php
	/**
	 *	Displaying blog post
	 **/
?>

<!-- Post view -->
<div class="postView">

	<!-- Post secondary -->
	<div class="postSecondary span-2">
		<div class="postDate">
			<?php $date = strtotime($item['Post']['publish_start']); ?>
			<div class="month"><?php echo $this->Text->truncate(CaracoleI18n::strftime("%B", $date), 3, array('ending' => '')); ?></div>
			<div class="day"><?php echo CaracoleI18n::strftime("%d", $date) ?></div>
			<div class="year"><?php echo CaracoleI18n::strftime("%Y", $date) ?></div>
		</div>
	</div>
	<!-- /Post secondary -->

	<!-- Post main -->
	<div class="postMain span-14 last">
		<?php
			// Title
			if (empty($hideTitle)) {
				echo $this->Html->tag('h3',
					$this->Fastcode->link(
						$item['Post']['name'],
						Post::url($item)
					)
				);
			}

			// Main text
			echo $this->Fastcode->div($item['Post']['text'], 'text');

			// Infos
			echo $this->Html->div('postFooter', null);
				// Tags
				if (!empty($item['Tag'])) {
					__d('caracole_blog', 'Tags :');
					echo $this->Html->tag('ul', null, array('class' => 'tags inline'));
					foreach($item['Tag'] as &$tag) {
						echo $this->Html->tag(
							'li',
							$this->Fastcode->link(
								$tag['name'],
								Tag::url(array('Tag' => $tag))
							)
						);
					}
					echo '</ul>';
				}

				// Comments
				echo $this->Fastcode->div(
					$this->Fastcode->link(
						($item['Post']['comment_count']==1) ? '1 comment' : sprintf('%1$s comments', $item['Post']['comment_count']),
						Router::url(Post::url($item))."#comments"
					),
					array('class' => 'comments')
				);
			echo '</div>';
		?>
	</div>
	<!-- /Post main -->

</div>
<!-- /Post view -->
