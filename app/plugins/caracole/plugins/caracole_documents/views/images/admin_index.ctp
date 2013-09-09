<?php

	/**
	 *	This file is a hook on the default admin_index.ctp file.
	 *	It is used to add a hook on the way images are displayed.
	 *
	 *
	 **/

	$this->set('pageCssClass', 'adminImagesIndex');


	// We will transform each cell content for a better display experience
	foreach($itemList as &$item) {
		$data = $item['Image'];
		$data['Version'] = $item['Version'];

		// We will add a preview
		$item['Image']['preview'] = array(
			'label' => $this->Image->image($data, array('resize' => 'square', 'width' => 96, 'height' => 96)),
			'escape' => false
		);

		// And a complete filename
		$item['Image']['filename'] = sprintf(
			'%1$s (%2$sx%3$s) - %4$s',
			$data['filename'].'.'.$data['ext'],
			$data['width'],
			$data['height'],
			CaracoleNumber::toHumanSize($data['filesize'])
		);
	}
	$this->set('itemList', $itemList);

	// Default view
	echo $this->element(
		'..'.DS.'admin'.DS.'admin_index',
		array('plugin' => 'caracole')
	);
