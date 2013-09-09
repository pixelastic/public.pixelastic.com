<?php
/**
 *	IconsController
 *	This controller is only used in the admin panel to generate the CSS Sprite used by the icons
 **/
class IconsController extends AppController {

	/**
	 *	admin_index
	 *	Main and only page. Will display the list of available icons and regenerate the sprite and CSS file if asked
	 **/
	function admin_index() {
		// If form submitted, we regenerate it
		if (!empty($this->data)) {
			$iconList = $this->model->generate($this->data['Icon']);

			// Adding a flash message
			$this->Session->setFlash(__d('caracole_icons', 'The new CSS Sprite file and corresponding CSS rules have been created.', true), 'success');

		} else {
			$iconList = $this->model->findAll();
		}

		// Passing it to the view
		$this->set(array(
			'iconList' => $iconList,
			'title_for_layout' => $this->model->translate('plural')
		));
	}



}
