<?php
App::import('Libs', 'CaracoleI18n.CaracoleI18n');

class CaracoleI18nTestCase extends CakeTestCase {

    function startTest() {
		Configure::write('I18n.languages', array('eng', 'fre'));
		Configure::write('Config.language', 'eng');
		Configure::write('I18n.default', 'eng');
		Configure::write('SiteUrl.lang', null);
	}

	// /fre/controller/action is in lang fre
	function testFindLangWithControllerAndAction() {
		$_GET = array('url' => 'fre/controller/action');
		CaracoleI18n::init();
		$result = Configure::read('Config.language');
		$this->assertEqual($result, 'fre');
	}

	// /fre/ is in lang fre
	function testFindLangWithOnlyLangAndSlash() {
		$_GET = array('url' => 'fre/');
		CaracoleI18n::init();
		$result = Configure::read('Config.language');
		$this->assertEqual($result, 'fre');
	}

	// /fre is in lang fre
	function testFindLangWithOnlyLangWithoutSlash() {
		$_GET = array('url' => 'fre');
		CaracoleI18n::init();
		$result = Configure::read('Config.language');
		$this->assertEqual($result, 'fre');
	}

	// / is in default lang
	function testSetDefaultLangIfNoneInUrl() {
		$_GET = array('url' => '');
		CaracoleI18n::init();
		$result = Configure::read('Config.language');
		$this->assertEqual($result, 'eng');
	}

	// /fre/ as an iso2 of fr
	function testSetCorrectIso2Lang() {
		$_GET = array('url' => 'fre/');
		CaracoleI18n::init();
		$result = Configure::read('Config.languageIso2');
		$this->assertEqual($result, 'fr');
	}

	// /fre/controller/action gets its lang removed from the url once it is parsed
	function testRemoveLangFromUrl() {
		$_GET = array('url' => 'fre/controller/action');
		CaracoleI18n::init();
		$result = $_GET['url'];
		$this->assertEqual($result, 'controller/action');
	}

	// /fre gets its url to /
	function testKeepOnlySlashInUrlWhenOnlyLangGiven() {
		$_GET = array('url' => 'fre');
		CaracoleI18n::init();
		$result = $_GET['url'];
		$this->assertEqual($result, '/');
	}

	// /fre/ gets its url to /
	function testKeepOnlySlashInUrlWhenOnlyLangGivenWithSlash() {
		$_GET = array('url' => 'fre/');
		CaracoleI18n::init();
		$result = $_GET['url'];
		$this->assertEqual($result, '/');
	}

	// /fr/controller/action does not get a lang
	function testTooShortLangDoesNotGetParsed() {
		$_GET = array('url' => 'fr/controller/action');
		CaracoleI18n::init();
		$result = Configure::read('Config.language');
		$this->assertEqual($result, 'eng');
	}

	// /foo keeps the default lang
	function testDoNotSetNonExistingLangs() {
		$_GET = array('url' => 'foo');
		CaracoleI18n::init();
		$result = Configure::read('Config.language');
		$this->assertEqual($result, 'eng');
	}

	// /foo does not gets parsed
	function testDoNotParseNonExistingLangs() {
		$_GET = array('url' => 'foo');
		CaracoleI18n::init();
		$result =  $_GET['url'];
		$this->assertEqual($result, 'foo');
	}

	// /foo does not get an iso2
	function testDoNotSetIso2ToNonExistingLangs() {
		$_GET = array('url' => 'foo');
		CaracoleI18n::init();
		$result = Configure::read('Config.languageIso2');
		$this->assertEqual($result, 'en');
	}

	



}
