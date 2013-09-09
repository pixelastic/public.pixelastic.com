<?php
	/**
	 *	Contact routes
	 **/

	Router::connect(
		'/contact/*',
		array('controller' => 'contacts', 'plugin' => 'caracole_contacts', 'action' => 'add')
	);
