<?php
	/**
	 *	Recent posts
	 **/

	echo $this->Html->tag('h3', __d('caracole_blog', 'Recent writings', true));

	echo $this->Html->tag('ul', null, array('class' => 'postRecent'));
	foreach($itemList as &$item) {
		echo $this->Html->tag('li', $this->Fastcode->link($item['Post']['name'], Post::url($item)));
	}

	echo '</ul>';


?>
