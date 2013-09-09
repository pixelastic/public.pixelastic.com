<?php
	/**
	 *	comment list
	 *	Display a list of all the comments of a given post
	 **/


?>
<!-- Comment list -->
<div class="commentIndex" id="comments">
	<?php
		echo $this->Html->tag('h3', __d('caracole_blog', 'Comments', true));

		// List of all comments
		if (empty($itemList)) {
			echo $this->Fastcode->div(
				$this->Fastcode->p(__d('caracole_blog', 'There are no comments on this post yet.', true)),
				'prepend-2 noComments'
			);
		}
		echo $this->Html->div('commentList');
		foreach($itemList as &$item) {
			echo $this->element('../comments/display', array(
				'plugin' => 'caracole_blog',
				'item' => $item
			));
		}

		// Spam blocked
		if ($spamCount>0) {
			echo $this->Html->div('spamCount', sprintf(__d('caracole_blog', '... and %1$s spam blocked', true), $spamCount));
		}

		echo '</div>';

		// Adding a new comment
		echo $this->Html->tag('h3', __d('caracole_blog', 'Adding a comment', true));

		echo $this->element('../comments/add', array(
			'plugin' => 'caracole_blog',
			'post_id' => $this->params['id']
		));

	?>
</div>
<!-- /Comment list -->