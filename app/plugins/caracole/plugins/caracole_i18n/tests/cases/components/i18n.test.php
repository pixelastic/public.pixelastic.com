<?php
// Testing controller
class TestingController extends Controller {
	var $components = array('Session');
    var $autoRender = false;
    function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }
    function render($action = null, $layout = null, $file = null) {
        $this->renderedAction = $action;
    }
    function _stop($status = 0) {
        $this->stopped = $status;
    }
}
class Testing { var $useTable = false; }
App::import('Component', 'CaracoleI18n.I18n');

// Mocks that will be needed
Mock::generatePartial('I18nComponent', 'MockI18nComponentgetPreferedLanguage', array('getPreferedLanguage'));
Mock::generatePartial('I18nComponent', 'MockI18nComponent__getL10nModel', array('__getL10nModel'));


class I18nTestCase extends CakeTestCase {

    function startTest() {
        // Controller
        $this->controller = new TestingController();
		$this->controller->constructClasses();
		$this->controller->Component->initialize($this->controller);
		$this->controller->beforeFilter();
		$this->controller->Component->startup($this->controller);

        // Component
        $this->i18n = new i18nComponent();


	}

    // Getting default language as preferred language if no match found
	function testGetDefaultLanguageAsPreferedLanguageIfNoMatchFound() {
		// Getting the mock of the L10n model
		App::import('L10n');
		Mock::generate('L10n', 'MockL10n');
		$l10n = new MockL10n();
		// Won't return a localFallback
		$l10n->setReturnValue('catalog', array('foo' => 'bar'));

		// Getting a partial mock of the component, we only need to mock the __getL10nModel to return a mocked one
		$i18n = new MockI18nComponent__getL10nModel();
		// Will return the mock L10n when asked
		$i18n->setReturnValue('__getL10nModel', &$l10n);

		// Default language
		Configure::write('I18n.default', 'foo');

		// Getting the language
		$result = $i18n->getPreferedLanguage();
		$this->assertEqual($result, 'foo');
	}

	// If session language is set, we won't use the prefered language but the one already defined
	function testDoNotChangeSessionLanguageIfAlreadySet() {
		// Setting session language
		$this->controller->Session->write('Config.language', 'foo');
		Configure::write('Config.language', 'fre'); // Actual language
		Configure::write('I18n.default', 'fre');	// Default language

		// Prefered language
		$i18n = new MockI18nComponentgetPreferedLanguage();
		$i18n->setReturnValue('getPreferedLanguage', 'bar');

		$i18n->initialize($this->controller);

		// Session should not be the prefered, but the actual language
		$result = $this->controller->Session->read('Config.language');
		$this->assertNotEqual($result, 'foo'); // Not the initial
		$this->assertNotEqual($result, 'bar'); // Not the prefered
		$this->assertEqual($result, 'fre'); // But the actual
	}

	// If a language is already defined, we won't change it
	function testDoNotChangeLanguageIfAlreadyDefined() {
		Configure::write('Config.language', 'foo');
		Configure::write('I18n.default', 'fre');

		$this->i18n->initialize($this->controller);

		$result = Configure::read('Config.language');
		$this->assertEqual($result, 'foo');

	}

	// Guessing language on first visit with default language
	function testGuessLanguageOnFirstVisitWithDefaultLanguage() {
		// Forcing first visit and default language
		Configure::write('Config.language', 'fre'); // Actual language
		Configure::write('I18n.default', 'fre');	// Default language
		Configure::write('I18n.languages', array('foo'));	//	Foo is available
		$this->controller->Session->delete('Config.language');	//	First visit

		// Creating i18n mock object
		$i18n = new MockI18nComponentgetPreferedLanguage();
		$i18n->setReturnValue('getPreferedLanguage', 'foo');

		$i18n->initialize($this->controller);

		// Asserting correct value in session
		$result = $this->controller->Session->read('Config.language');
		$this->assertEqual($result, 'foo');
		// Asserting correct value in configure
		$result = Configure::read('Config.language');
		$this->assertEqual($result, 'foo');
	}

	// Save the siteUrl with language url
	function testSetSiteUrlLangDefaultLanguage() {
		Configure::write('Config.language', 'fre'); // Actual language
		Configure::write('I18n.default', 'fre');	// Default language
		Configure::write('SiteUrl.default', 'http://test.com/');

		$this->i18n->initialize($this->controller);
		$result = Configure::read('SiteUrl.lang');
		$this->assertEqual($result, 'http://test.com/');
	}

	// Initiating the I18n process should create a SiteUrl.lang value, keeping the default if default language
	function testSetSiteUrlLangNotDefaultDefault() {
		Configure::write('Config.language', 'eng'); // Actual language
		Configure::write('I18n.default', 'fre');	// Default language
		Configure::write('SiteUrl.default', 'http://test.com/');

		$this->i18n->initialize($this->controller);
		$result = Configure::read('SiteUrl.lang');
		$this->assertEqual($result, 'http://test.com/eng/');
	}

	// On first visit, do not redirect to preferred language if this language is not available
	function testDoNotRedirectToUnavailableLanguage() {
		// Forcing first visit and default language
		Configure::write('Config.language', 'fre'); // Actual language
		Configure::write('I18n.default', 'fre');	// Default language
		Configure::write('I18n.languages', array());	//	No other available language
		$this->controller->Session->delete('Config.language');	//	First visit

		// Creating i18n mock object
		$i18n = new MockI18nComponentgetPreferedLanguage();
		$i18n->setReturnValue('getPreferedLanguage', 'eng');

		$i18n->initialize($this->controller);

		// Stay with current language because preferred is not available
		$result = $this->controller->Session->read('Config.language');
		$this->assertEqual($result, 'fre');
		// Asserting correct value in configure
		$result = Configure::read('Config.language');
		$this->assertEqual($result, 'fre');
	}

}
