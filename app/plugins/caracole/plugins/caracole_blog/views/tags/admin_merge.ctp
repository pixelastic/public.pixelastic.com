<?php
	/**
	 *	Merge two tags
	 *	Will move all the associated posts from one tag to another and then delete the initial tag
	 *
	 *	TODO : This page needs refactoring, it is broken (missing title element)
	 **/

	echo $this->element('../admin/admin_edit', array('plugin' => 'caracole'));