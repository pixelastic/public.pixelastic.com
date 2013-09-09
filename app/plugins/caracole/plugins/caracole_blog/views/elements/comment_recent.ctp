<?php
	/**
	 *	Recent comments
	 **/

	echo $this->Html->tag('h3', __d('caracole_blog', 'Recent comments', true));

	echo $this->Html->tag('ul', null, array('class' => 'commentRecent'));
	foreach($itemList as &$item) {
		echo $this->Html->tag('li',
			$this->Html->image(
				'http://www.gravatar.com/avatar/'.md5($item['Comment']['email']).'?d=identicon&s=20',
				array('alt' => $item['Comment']['author'])
			).
			$this->Fastcode->link(
				$this->Fastcode->truncate($item['Comment']['text'], 40),
				Router::url(Post::url($item)).'#comment'.$item['Comment']['id']
			)
		);
	}

	echo '</ul>';


?>
