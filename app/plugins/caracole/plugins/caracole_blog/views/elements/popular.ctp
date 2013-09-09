<?php
	/**
	 *	Popular tags
	 **/

	echo $this->Html->tag('h3', __d('caracole_blog', 'Popular tags', true));

	echo $this->Html->tag('ul', null, array('class' => 'popular'));
	foreach($itemList as &$item) {
		echo $this->Html->tag('li', $this->Fastcode->link($item['Tag']['name'], Tag::url($item)));
	}

	echo '</ul>';


?>
