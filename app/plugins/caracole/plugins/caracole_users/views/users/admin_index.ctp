<?php

	/**
	 *	This file is a hook on the default admin_index.ctp file.
	 *	It is used to transform user statuses in icons
	 **/
	// We find the most appropriate status
	function findMostAppropriateStatus($data, &$fastcode) {
		/* Icons are commented out because the display escape HTML chars */
		// Disabled
		if (!empty($data['is_disabled'])) return array('icon' => 'User_disabled', 'label' => __d('caracole_users', 'Disabled', true));
		// Master
		if (!empty($data['is_master'])) return array('icon' => 'User_master', 'label' => __d('caracole_users', 'Master admin', true));
		//Admin
		if (!empty($data['is_admin'])) return array('icon' => 'User_admin', 'label' => __d('caracole_users', 'Admin', true));
		//Member
		if (!empty($data['is_member'])) return array('icon' => 'User_member',  'label' => __d('caracole_users', 'Member', true));
		// Default
		return array('icon' => 'User', 'label' => __d('caracole_users', 'User', true));
	}

	// Finding current model
	$modelName = $this->Fastcode->model();

	foreach($itemList as &$item) {
		$item[$modelName]['status'] = findMostAppropriateStatus($item[$modelName], $this->Fastcode);
	}
	$this->set('itemList', $itemList);

	// Default view
	echo $this->element(
		'..'.DS.'admin'.DS.'admin_index',
		array('plugin' => 'caracole')
	);