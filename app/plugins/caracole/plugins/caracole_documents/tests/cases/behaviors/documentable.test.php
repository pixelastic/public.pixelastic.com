<?php
App::import('Behavior', 'CaracoleDocuments.Documentable');


// Dummy Page class : related to a document
class Page extends CakeTestModel {
	var $name = 'Page';
	var $actsAs = array('Documentable', 'Containable');
	var $belongsTo = array('File' => array('className' => 'CaracoleDocuments.Document', 'foreignKey' => 'document_file'));
	var $adminSettings = array();
}
// Dummy Tag class : NOT related to a document
class Tag extends CakeTestModel {
	var $name = 'Tag';
	var $actsAs = array('Documentable', 'Containable');
	var $adminSettings = array();
}


class DocumentTestCase extends CakeTestCase {
	var $fixtures = array(
		'plugin.caracole_documents.document',
		'plugin.caracole_documents.metadata',
		'plugin.caracole_documents.document_page',
		'plugin.caracole_documents.document_tag'
	);

	function startTest() {
		ClassRegistry::flush();
		$this->page = ClassRegistry::init('Page');
		$this->tag = ClassRegistry::init('Tag');
	}

	 // Behavior is enabled to model related to a Document
	function testBehaviorEnabledToPage() {
		$result = $this->page->Behaviors->enabled('Documentable');
		$this->assertTrue($result);
	}

	// Behavior is disabled to models not related to a Document
	function testBehaviorDisabledToTag() {
		$this->tag->Behaviors->disable('Documentable');
		$result = $this->tag->Behaviors->enabled('Documentable');
		$this->assertFalse($result);
	}

	// Finding a Page by Id will correctly get all associated metadatas
	function testGettingAPageWillGetMetadata() {
		$result = $this->page->find('first', array('conditions' => array('Page.id' => 2)));
		$item = $this->page->find('first', array('conditions' => array('Page.id' => 2)));;
		$this->assertEqual($result['File']['author'], 'myself');
	}

	 // Manually setting a contain of false won't get the Metadata (nor the documents)
	function testDontGettingAnyDocumentIfNoContain() {
		$result = $this->page->find('first', array('conditions' => array('Page.id' => 2), 'contain' => false));
		$this->assertTrue(empty($result['Document']));
	}

	// Manually setting a negative recursivity won't get the Metadata (nor the documents)
	function testDontGettingAnyDocumentIfNegativeRecursive() {
		$result = $this->page->find('first', array('conditions' => array('Page.id' => 2), 'contain' => false));
		$this->assertTrue(empty($result['Document']));
	}

}
?>