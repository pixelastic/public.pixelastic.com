<?php
/**
 *	WorksController
 **/
class WorksController extends AppController {

	/**
	 *	index
	 *	We display the whole list of works
	 **/
	function index() {
		// Getting the whole list
		$itemList = $this->model->find('all');

		$this->set('itemList', $itemList);
	}

}
