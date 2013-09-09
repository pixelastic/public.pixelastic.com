<?php

	/**
	 *	This file is a hook on the default admin_index.ctp file.
	 *	It is used to display informations in a more readable format
	 **/
	foreach($itemList as &$item) {
		// Stripping content
		$item['Comment']['text'] = $this->Fastcode->truncate($item['Comment']['text']);

		// Adding a class os "isSpam" if this comment is a spam
		if (!empty($item['Comment']['is_spam'])) {
			foreach($item['Comment'] as $key => $value) {
				// Skipping the id key because we will still need an easy access to that
				if ($key=='id') continue;
				$item['Comment'][$key] = array('label' => $value, 'class' => 'isSpam');
			}
		}
	}
	$this->set('itemList', $itemList);

	// We also remove the name column
	unset($headers['Comment.id']);
	$this->set('headers', $headers);

	// Default view
	echo $this->element('..'.DS.'admin'.DS.'admin_index', array('plugin' => 'caracole'));
