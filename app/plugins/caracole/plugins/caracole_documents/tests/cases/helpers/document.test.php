<?php
App::import('Core', array('Controller', 'View'));
App::import('Helper', array(
	'Form', 'Html', 'Time',
	'Caracole.CaracoleForm', 'Caracole.CaracoleHtml', 'Caracole.Fastcode',
	'CaracoleIcons.Icon',
	'CaracoleDocuments.Document'
));
class DocumentHelperTestCase extends CakeTestCase {

	function startTest() {
		// Init both controller and view, used by the main helper
		$this->controller =& new Controller();
		$this->view =& new View($this->controller);

		// Init needed helpers
		$this->helper = &new DocumentHelper();
		$this->helper->Html = &new HtmlHelper();

		$this->helper->Fastcode = &new FastcodeHelper();
		$this->helper->Fastcode->Time = &new TimeHelper();
		$this->helper->Fastcode->CaracoleHtml = &new CaracoleHtmlHelper();
			$this->helper->Fastcode->CaracoleHtml->Html = $this->helper->Html;
			$this->helper->Fastcode->CaracoleHtml->Fastcode= $this->helper->Fastcode;
		$this->helper->Fastcode->CaracoleForm = &new CaracoleFormHelper();
			$this->helper->Fastcode->CaracoleForm->Form = &new FormHelper();
			$this->helper->Fastcode->CaracoleForm->Fastcode= $this->helper->Fastcode;
		$this->helper->Fastcode->Icon = &new IconHelper();
			$this->helper->Fastcode->Icon->Html = $this->helper->Html;


		$this->helper->Fastcode->beforeRender();	// Loads all helpers in Fastcode

		$this->data = array(
			'id' => 'uuid',
			'ext' => 'txt',
			'mimetype' => 'text/plain',
			'filename' => 'foo',
			'filesize' => 3,
			'path' => 'foo/bar/baz/uuid.text'
		);

	}

	// Returns the url of the element
	function testGetUrl() {
		$result = $this->helper->url($this->data);
		$this->assertEqual($result, '/foo/bar/baz/uuid/foo.txt');
	}

	// We can change the downloadable name
	function testGetUrlWithArbitraryDownloadName() {
		$this->data['filename'] = 'blah';
		$result = $this->helper->url($this->data);
		$this->assertEqual($result, '/foo/bar/baz/uuid/blah.txt');
	}

	// We can change the downloadable ext
	function testGetUrlWithArbitraryDownloadExt() {
		$this->data['ext'] = 'bar';
		$result = $this->helper->url($this->data);
		$this->assertEqual($result, '/foo/bar/baz/uuid/foo.bar');
	}

	// We can make absolute links
	function testGetUrlWithAbsoluteUrl() {
		$result = $this->helper->url($this->data, true);
		$this->assertEqual($result, Configure::read('SiteUrl.default').'foo/bar/baz/uuid/foo.txt');
	}

	// Guess icons based on extension
	function testGuessImage() {
		$this->assertEqual($this->helper->getIcon('jpg'), 'image');
		$this->assertEqual($this->helper->getIcon('doc'), 'text');
		$this->assertEqual($this->helper->getIcon('zip'), 'archive');
		$this->assertEqual($this->helper->getIcon('pdf'), 'pdf');
	}

	// Revert to default icon if unknown
	function testDefaultIcon() {
		$this->assertEqual($this->helper->getIcon('xyz'), 'Document');
	}

	// Preview will return a simple link with extension
	function testPreviewDefault() {
		$result = $this->helper->preview($this->data);
		$this->assertTags($result, array(
			'a' => array(
				'href' => '/foo/bar/baz/uuid/foo.txt',
				'target' => '_blank',
				'title' => 'foo.txt (3.00 B)'
			),
				'span' => array('class' => 'icon iconText'),
				'/span',
				'foo.txt (3.00 B)',
			'/a'
		));
	}

	// Can define preview label
	function testPreviewChangeLabel() {
		$result = $this->helper->preview($this->data, array('label' => 'label'));
		$this->assertTags($result, array(
			'a' => array(
				'href' => '/foo/bar/baz/uuid/foo.txt',
				'target' => '_blank',
				'title' => 'label'
			),
				'span' => array('class' => 'icon iconText'),
				'/span',
				'label',
			'/a'
		));
	}

	// Can override default target
	function testPreviewChangeTarget() {
		$result = $this->helper->preview($this->data, array('target' => false));
		$this->assertTags($result, array(
			'a' => array(
				'href' => '/foo/bar/baz/uuid/foo.txt',
				'title' => 'foo.txt (3.00 B)'
			),
				'span' => array('class' => 'icon iconText'),
				'/span',
				'foo.txt (3.00 B)',
			'/a'
		));
	}

	// Can override default url
	function testPreviewChangeUrl() {
		$result = $this->helper->preview($this->data, array('url' => 'blah.txt'));
		$this->assertTags($result, array(
			'a' => array(
				'href' => '/blah.txt',
				'target' => '_blank',
				'title' => 'foo.txt (3.00 B)'
			),
				'span' => array('class' => 'icon iconText'),
				'/span',
				'foo.txt (3.00 B)',
			'/a'
		));
	}

	// Can override default icon
	function testPreviewChangeIcon() {
		$result = $this->helper->preview($this->data, array('icon' => 'success'));
		$this->assertTags($result, array(
			'a' => array(
				'href' => '/foo/bar/baz/uuid/foo.txt',
				'target' => '_blank',
				'title' => 'foo.txt (3.00 B)'
			),
				'span' => array('class' => 'icon iconSuccess'),
				'/span',
				'foo.txt (3.00 B)',
			'/a'
		));
	}



}
?>