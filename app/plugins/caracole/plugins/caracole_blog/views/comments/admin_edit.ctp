<?php

	/**
	 *	This file is a hook on the default admin_edit.ctp file.
	 *	It is used to display some fields in a more readable format
	 **/

	// Javascript enabled
	$this->data['Comment']['spam_js'] = empty($this->data['Comment']['spam_js']) ? __d('caracole_blog', 'Not enabled', true) : __d('caracole_blog', 'Enabled', true);

	// Delay before posting
	$nbrMinutes = floor($this->data['Comment']['spam_delay']/60);
	$nbrSeconds = $this->data['Comment']['spam_delay']%60;
	$this->data['Comment']['spam_delay'] = sprintf('%1$s minutes and %2$s seconds', $nbrMinutes, $nbrSeconds);

	// Passing those data to the helper responsible for displaying them
	$this->Form->data = $this->data;

	// Default view
	echo $this->element('..'.DS.'admin'.DS.'admin_edit', array('plugin' => 'caracole'));
