<?php

	/**
	 *	This file is a hook on the default admin_index.ctp file.
	 *	It is used to display dates in a more human-readable form
	 **/
	foreach($itemList as &$item) {
		$item['Post']['publish_start'] = strftime(__d('caracole_blog', "%d %B %Y at %H:%M", true), strtotime($item['Post']['publish_start']));
	}
	$this->set('itemList', $itemList);

	// Default view
	echo $this->element('..'.DS.'admin'.DS.'admin_index', array('plugin' => 'caracole'));
