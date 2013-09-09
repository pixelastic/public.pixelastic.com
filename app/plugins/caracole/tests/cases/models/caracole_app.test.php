<?php
App::import('Model', 'Caracole.CaracoleApp');

// Dummy class
class Page extends CaracoleAppModel {
	var $adminSettings = array();
	var $order = array('Page.name' => 'ASC');
}

class CaracoleAppModelTestCase extends CakeTestCase {
	var $fixtures = array(
		'plugin.caracole.caracole_app_page'
	);

	function startTest() {
		$this->model = ClassRegistry::init('Page');
	}

	/**
	 *	We make sure that any additional behavior we may have added (like in CaracoleAppModel::find()) would not erase
	 *	the default search values
	 **/
	function testGetRecordsByOrder() {
		$this->model->order = array('Page.name' => 'DESC');
		$result = $this->model->find('all');
		$this->assertEqual($result[0]['Page']['name'], 'Bibliography');
	}






  }
  ?>