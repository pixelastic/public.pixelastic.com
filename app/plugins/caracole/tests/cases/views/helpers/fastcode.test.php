<?php
//App::import('Core', array('Controller', 'View'));
App::import('Helper', array(
	'Caracole.Fastcode',
	'Caracole.CaracoleForm',
	'Caracole.CaracoleHtml',
	'CaracoleIcons.Icon'
));
class CaracoleFormTestCase extends CakeTestCase {

	function startTest() {
		// Init needed helpers
		$this->helper = &new FastcodeHelper();
		$this->helper->CaracoleHtml = &new CaracoleHtmlHelper();
		$this->helper->CaracoleForm = &new CaracoleFormHelper();
		$this->helper->Icon = &new IconHelper();
	}


	// Gets the current model from the helper parames
	function testGetModel() {
		$this->helper->params = array('models' => array('Foo', 'Bar'));
		$result = $this->helper->model();
		$this->assertEqual($result, 'Foo');
	}


}
?>