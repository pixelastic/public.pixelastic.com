<?php
	/**
	 *	View page.
	 *	Same as an edit page, but only display informations without possibility to edit it
	 **/

	echo $this->element('../admin/admin_edit', array('plugin' => 'caracole'));
	// Special page css
	$this->set('pageCssId', 'adminView');
