<?php
/**
 *	PagesController
 *	Helps in displaying classic pseudo-dynamic pages.
 **/
class PagesController extends AppController {

	/**
	 *	admin_home
	 *	Home page of the admin panel
	 **/
	function admin_home() {


	}


	/**
	 *	view
	 *	Displays a page based on its slug.
	 *	The method will first look for a page with the specified slug in the database.
	 *	Then it will use the most appropriate view : default to view.ctp unless there is a view that match the slug.
	 *	Default views are fetched from the plugin directory but you can also drop files in your main app/views/pages
	 **/
	function view($pageSlug = null) {
		//	If no slug set, then it's an error
		if (empty($pageSlug)) {
			return $this->cakeError('error404');
		}

		//	Finding the page
		$item = $this->model->find('first', array(
			'conditions' => array('Page.slug' => $pageSlug)
		));
		// If the page is found, we send info to the view and prepare to render the view
		if (!empty($item)) {
			$this->set('item', $item);
		}

		// We render a special view that will take care of selecting the correct view to display
		$this->render('dispatch_view');
	}



}
