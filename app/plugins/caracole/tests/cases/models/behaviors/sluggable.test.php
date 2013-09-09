<?php
App::import('Behavior', 'Caracole.Sluggable');
class Page extends CakeTestModel {
	var $actsAs = array('Caracole.Sluggable');
	var $adminSettings = array();

}


class DraftableTestCase extends CakeTestCase {
	var $fixtures = array(
		'plugin.caracole.sluggable_page',
	);

	function startTest() {
		ClassRegistry::flush();
		$this->model = ClassRegistry::init('Page');

		// Forcing english as current language
		Configure::write('Config.language', 'eng');
	}

	// Add a slug field to the admin panel
	function testAddSlugFieldToAdminPanel() {
		$result = $this->model->adminSettings['fields'];
		$this->assertTrue(!empty($result['slug']));

	}

	// Update slug only if update display field
	function testDoNotUpdateSlugIfNoChangeInDisplayField() {
		$this->model->create(array('id' => 1));
		$this->model->save();
		$result = $this->model->find('first', array('conditions' => array('Page.id' => 1)));
		$this->assertEqual($result['Page']['slug'], 'test');
	}

	// Use display field if no slug specified
	function testUseDisplayFieldAsBaseSlug() {
		$this->model->create(array('id' => 1, 'name' => 'foo'));
		$this->model->save();
		$result = $this->model->find('first', array('conditions' => array('Page.id' => 1)));
		$this->assertEqual($result['Page']['slug'], 'foo');
	}

	// Save a slug of a complete sentence
	function testSaveSlugFromCompleteSentence() {
		$this->model->create(array('id' => 1, 'name' => "How to write a great test"));
		$this->model->save();
		$result = $this->model->find('first', array('conditions' => array('Page.id' => 1)));
		$this->assertEqual($result['Page']['slug'], 'write-great-test');
	}

	// Revert to classic slug if only made of common words
	function testUseClassicSlugIfOnlyComposedOfCommonWords() {
		$this->model->create(array('id' => 1, 'name' => "Don't tell me what I can't do"));
		$this->model->save();
		$result = $this->model->find('first', array('conditions' => array('Page.id' => 1)));
		$this->assertEqual($result['Page']['slug'], 'don-t-tell-me-what-i-can-t-do');
	}

	// Revert to hash if no slug available at all
	function testUseRandomHashIfNoSlugAvailable() {
		$this->model->create(array('id' => 1, 'name' => "?!"));
		$this->model->save();
		$result = $this->model->find('first', array('conditions' => array('Page.id' => 1)));
		$this->assertEqual($result['Page']['slug'], md5("?!"));

	}




}
?>