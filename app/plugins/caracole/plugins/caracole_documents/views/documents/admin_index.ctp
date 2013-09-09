<?php

	/**
	 *	This file is a hook on the default admin_index.ctp file.
	 *	It is used to add a hook on the way documents are displayed.
	 *
	 *
	 **/

	// We will get rid of the id column and replace it with the Metadata.filename one
	unset($headers['Document.id']);
	$this->set('headers', $headers);

	// We will also transform each ext in an icon
	foreach($itemList as &$item) {
		$item['Document']['ext'] = array('icon' => $this->Document->getIcon($item['Document']['ext']), 'label' => $item['Document']['ext']);
	}
	$this->set('itemList', $itemList);

	// Default view
	echo $this->element(
		'..'.DS.'admin'.DS.'admin_index',
		array('plugin' => 'caracole')
	);
