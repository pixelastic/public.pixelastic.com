<?php
App::import('Helper', 'Caracole.CaracoleApp');
class CaracoleAppHelperTestCase extends CakeTestCase {

	function startTest() {
		$this->helper = new CaracoleAppHelper();

	}

	// Prepending the current lang to every url if not the default one
	function testPrependCurrentLangToUrl() {
		Configure::write('Config.language', 'fre');
		Configure::write('I18n.default', 'eng');
		$url = array('controller' => 'controller', 'action' => 'action');
		$result = $this->helper->url($url);
		$expected = '/fre/controller/action';
		$this->assertEqual($result, $expected);
	}

	// Do not prepend any lang to the url if the default one
	function testDoNotPrependLangToUrlIfDefault() {
		Configure::write('Config.language', 'eng');
		Configure::write('I18n.default', 'eng');
		$url = array('controller' => 'controller', 'action' => 'action');
		$result = $this->helper->url($url);
		$expected = '/controller/action';
		$this->assertEqual($result, $expected);
	}

	// Prepending lang even for absolute url
	function testPrependLangToAbsoluteUrl() {
		Configure::write('Config.language', 'fre');
		Configure::write('I18n.default', 'eng');
		$url = array('controller' => 'controller', 'action' => 'action');
		$result = $this->helper->url($url, true);
		$expected = FULL_BASE_URL.'/fre/controller/action';
		$this->assertEqual($result, $expected);
	}

	// Passing an absolute url in form of string to an url gets returned directly
	function testPassingWithoutChangeAbsoluteStringUrl() {
		$url = 'http://test.com';
		$result = $this->helper->url($url);
		$this->assertEqual($result, $url);
	}




}
?>