<?php
//App::import('Core', 'Model');
//App::import('Model', 'Caracole.CaracoleAppModel');
App::import('Behavior', 'Caracole.Draftable');
// Dummy classes
/*
 class Comment extends CakeTestModel {
	var $actsAs = array('Caracole.Draftable');
	var $belongsTo = array('Page');
	var $adminSettings = array();
}
*/
class Page extends CakeTestModel {
	var $actsAs = array('Caracole.Draftable');
	//var $hasMany = array('Comment');
	var $adminSettings = array();

}


class DraftableTestCase extends CakeTestCase {
	var $fixtures = array(
		'plugin.caracole.draftable_page',
		/*'plugin.caracole.draftable_comment'*/
	);

	function startTest() {
		ClassRegistry::flush();
		$this->model = ClassRegistry::init('Page');
	}

	// Will add a field for setting as draft in the admin panel
	function testAllowCheckboxOptionInAdminPanelToSetAsDraft() {
		$this->assertNotNull($this->model->adminSettings['fields']['is_draft']);
	}

	// Will get all drafted elements in the admin panel
	function testWillGetAllDraftedElementsInAdminPagination() {
		$result = $this->model->adminSettings['index']['paginate']['Page']['conditions']['Page.is_draft'];
		$this->assertEqual($result, array(0,1));
		$result = $this->model->adminSettings['index']['paginate']['Page']['fields'];
		$this->assertTrue(in_array('Page.is_draft', $result));
	}

	// Tests that a model with this behavior won't retrieve records sets as drafts
	function testDontGetDraftItems() {
		$result = $this->model->find('all');
		$this->assertEqual(count($result), 1);
	}

	// Returns all items if disabling behavior for this call
	function testGetAllRecordsIfBehaviorDisabledForNextCall() {
		$this->model->disableDraftable();
		$result = $this->model->find('all');
		$this->assertEqual(count($result), 2);
	}

	// Disabling behavior only work for next call
	function testDisablingOnlyLastForOneCall() {
		$this->model->disableDraftable();
		$result = $this->model->find('all');
		$this->assertEqual(count($result), 2);
		$result = $this->model->find('all');
		$this->assertEqual(count($result), 1);
	}

	// Disabling permanently behavior works for all calls
	function testDisablingPermanentlyLastForNumerousCalls() {
		$this->model->disableDraftablePermanently();
		$result = $this->model->find('all');
		$this->assertEqual(count($result), 2);
		$result = $this->model->find('all');
		$this->assertEqual(count($result), 2);
	}

	// Will not get a specific record if marked as draft
	function testWillNotGetSpecificDraftRecord() {
		$result = $this->model->find('first', array('conditions' => array('Page.id' => 2)));
		$this->assertFalse($result);
	}

	// Will correctly return the specified record if is_draft condition passed
	function testDoNotOverrideFindConditionsOnDraft() {
		$result = $this->model->find('first', array('conditions' => array('Page.id' => 2, 'Page.is_draft' => 1)));
		$this->assertTrue($result);
	}




}
?>