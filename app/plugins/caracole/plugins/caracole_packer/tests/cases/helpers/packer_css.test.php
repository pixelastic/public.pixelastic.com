<?php
App::import('Helper', 'CaracolePacker.Packer');
App::import('Helper', 'CaracolePacker.PackerCss');


class PackerCssControllerTestCase extends CakeTestCase {

	// Getting the content of various test files
	function getCss($files) {
		if (!is_array($files)) $files = array($files);
		$content = '';
		foreach($files as &$file) {
			$content.=file_get_contents(CARACOLE.'plugins'.DS.'caracole_packer'.DS.'tests'.DS.'sources'.DS.$file);
		}
		return $content;
	}

	function startTest() {
		$this->packer = new PackerCssHelper();
	}

	// Keep @-moz-document url-prefix() {} "hacks"
	function testKeepMozDocumentUrlPrefix() {
		$initial = $this->getCss('mozDocumentUrlPrefix.css');
		$expected = $this->getCss('mozDocumentUrlPrefix.expected.css');
		$result = $this->packer->compress($initial);
		$this->assertEqual($result, $expected);
	}




}
