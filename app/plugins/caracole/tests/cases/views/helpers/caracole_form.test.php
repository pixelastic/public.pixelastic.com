<?php
App::import('Core', array('Controller', 'View'));
App::import('Helper', array(
	'Form', 'Html', 'Time',
	'Caracole.CaracoleForm', 'Caracole.CaracoleHtml', 'Caracole.Fastcode',
	'CaracoleIcons.Icon',
	'CaracoleDocuments.Document'
));
class CaracoleFormTestCase extends CakeTestCase {

	function startTest() {
		// Init both controller and view, used by the main helper
		$this->controller =& new Controller();
		$this->view =& new View($this->controller);

		// Init needed helpers
		$this->helper = &new CaracoleFormHelper();
		$this->helper->Html = &new HtmlHelper();
		$this->helper->Form = &new FormHelper();
			$this->helper->Form->Html = $this->helper->Html;

		$this->helper->Document = &new DocumentHelper();

		$this->helper->Fastcode = &new FastcodeHelper();
		$this->helper->Fastcode->Time = &new TimeHelper();
		$this->helper->Fastcode->CaracoleHtml = &new CaracoleHtmlHelper();
			$this->helper->Fastcode->CaracoleHtml->Html = $this->helper->Html;
			$this->helper->Fastcode->CaracoleHtml->Fastcode= $this->helper->Fastcode;
		$this->helper->Fastcode->CaracoleForm = $this->helper;
			$this->helper->Fastcode->CaracoleForm->Form = $this->helper->Form;
			$this->helper->Fastcode->CaracoleForm->Fastcode= $this->helper->Fastcode;
		$this->helper->Fastcode->Icon = &new IconHelper();
			$this->helper->Fastcode->Icon->Html = $this->helper->Html;


		$this->helper->Fastcode->beforeRender();	// Loads all helpers in Fastcode

	}


	// Displays a button tag with type = button and a tabindex
	function testButton() {
		$expected = array(
			'button' => array('tabindex' => 1, 'type' => 'button'),
			'foo',
			'/button'
		);
		$result = $this->helper->button('foo');
		$this->assertTags($result, $expected);
	}

	// Adds an icon to the button
	function testAddIconToButton() {
		$expected = array(
			'button' => array('tabindex' => 1, 'type' => 'button'),
				'span' => array('class' => 'icon iconAdd'),
				'/span',
			'foo',
			'/button'
		);
		$result = $this->helper->button('foo', array('icon' => 'add'));
		$this->assertTags($result, $expected);
	}



	// Incrementing tabindex on each call
	function testTabindexIncrement() {
		$this->assertEqual($this->helper->tabindex(), 1);
		$this->assertEqual($this->helper->tabindex(), 2);
		$this->assertEqual($this->helper->tabindex(), 3);
	}



	// Useless styling keys (after, div, tabindex) shouldn't be passed to hidden fields
	function testDiscardDivKeyFromInputHidden() {
		$result = $this->helper->input('Test.foo', array('type' => 'hidden'));
		$expected = array(
			'input' => array(
				'type' => 'hidden',
				'name' => 'data[Test][foo]',
				'id' => 'TestFoo'
			)
		);
		$this->assertTags($result, $expected);
	}

	// Can pass a secureValue key on hidden field to not secure the value
	function testHiddenCanUnsecureTheValue() {
		Mock::generatePartial('FormHelper', 'MockFormHelperSecure', array('__secure'));
		$this->helper->Form = new MockFormHelperSecure();

		$this->helper->hidden('Test.foo', array('secureValue'));
		$this->helper->Form->expectOnce('__secure', array());
	}


	// Guess its a select if options are set
	function testGuessItsSelectIfOptionsAreSet() {
		$result = $this->helper->__guessInputType('Test.foo', array('options' => array()));
		$this->assertEqual($result, 'select');
	}

	// Guess its a password
	function testGuessPassword() {
		$this->assertEqual($this->helper->__guessInputType('Test.pass'), 'password');
		$this->assertEqual($this->helper->__guessInputType('Test.psword'), 'password');
		$this->assertEqual($this->helper->__guessInputType('Test.pwd'), 'password');
		$this->assertEqual($this->helper->__guessInputType('Test.password'), 'password');
	}

	// Guess its a checkbox
	function testGuessCheckbox() {
		$result = $this->helper->__guessInputType('Test.is_foo');
		$this->assertEqual($result, 'checkbox');
	}

	// Guess its a belongsTo relationship
	function testGuessBelongsTo() {
		$this->assertEqual($this->helper->__guessInputType('Test.foo_id'), 'belongsTo');
	}

	// Guess its an editor
	function testGuessTextarea() {
		$this->assertEqual($this->helper->__guessInputType('Test.text'), 'editor');
		$this->assertEqual($this->helper->__guessInputType('Test.content'), 'editor');
	}

	// Guess its an hidden field if its the primary key
	function testGuessHiddenForPrimaryField() {
		$this->helper->Form->fieldset = array('Test' => array('key' => 'bar'));

		$result = $this->helper->__guessInputType('Test.bar');
		$this->assertEqual($result, 'hidden');
	}

	// Guess document
	function testGuessDocumentField() {
		$this->assertEqual($this->helper->__guessInputType('Test.document_foo'), 'document');
	}

	// Revert to null if no guess found (let cake decide)
	function testGuessTextAsDefault() {
		$this->assertNull($this->helper->__guessInputType('Test.foo'));
	}

	// Keep the specified type and do not guess if one is set
	function testDoNotGuessIfSpecified() {
		$result = $this->helper->__guessInputType('Test.foo', array('type' => 'bar'));
		$this->assertEqual($result, 'bar');
	}

	// Default input output
	function testInputDefault() {
		$result = $this->helper->input('Test.foo');
		$expected = array(
			'div' => array('class' => 'input'),
				'label' => array('for' => 'TestFoo'),
					'Foo',
				'/label',
				'input' => array(
					'name' => 'data[Test][foo]',
					'type' => 'text',
					'tabindex' => 1,
					'id' => 'TestFoo'
				),
			'/div'
		);
		$this->assertTags($result, $expected);
	}


	// Use the options as label for the field
	function testInputUseOptionAsLabel() {
		$result = $this->helper->input('Test.foo', 'Label');
		$expected = array(
			'div' => array('class' => 'input'),
				'label' => array('for' => 'TestFoo'),
					'Label',
				'/label',
				'input' => array(
					'name' => 'data[Test][foo]',
					'type' => 'text',
					'tabindex' => 1,
					'id' => 'TestFoo'
				),
			'/div'
		);
		$this->assertTags($result, $expected);
	}

	// Remove label completely if set to false
	function testInputRemoveLabel() {
		$result = $this->helper->input('Test.foo', array('label' => false));
		$expected = array(
			'div' => array('class' => 'input'),
				'input' => array(
					'name' => 'data[Test][foo]',
					'type' => 'text',
					'tabindex' => 1,
					'id' => 'TestFoo'
				),
			'/div'
		);
		$this->assertTags($result, $expected);
	}


	// Remove hard (if not impossible) to style legend element from radio inputs
	function testRemoveLegendFromRadio() {
		$result = $this->helper->input('Test.foo', array('type' => 'radio', 'options' => array('0' => 'No', '1' => 'Yes')));
		$expected = array(
			array('div' => array('class' => 'input radio')),
				array('div' => array('class' => 'label')),
					'Foo',
				'/div',
				array('div' => array('class' => 'radios')),
					array('input' => array('type' => 'hidden', 'name' => 'data[Test][foo]', 'id' => 'TestFoo_', 'value' => '')),
					array('input' => array('type' => 'radio', 'name' => 'data[Test][foo]', 'id' => 'TestFoo0', 'tabindex' => 1, 'value' => 0)),
					array('label' => array('for' => 'TestFoo0')),
						'No',
					'/label',
					array('input' => array('type' => 'radio', 'name' => 'data[Test][foo]', 'id' => 'TestFoo1', 'tabindex' => 1, 'value' => 1)),
					array('label' => array('for' => 'TestFoo1')),
						'Yes',
					'/label',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);
	}

	// Set datetime fields in two field, one for the date and one for the time
	function testSetDateTimeAsTwoDifferentFields() {
		$result = $this->helper->input('Test.foo', array('type' => 'datetime'));
		$expected = array(
			array('div' => array('class' => 'input datetime')),
				array('label' => array('for' => 'TestFooDate')),
					'Foo',
				'/label',
				array('input' => array('name' => 'data[Test][fooDate]', 'type' => 'text', 'tabindex' => 1, 'class' => 'date', 'id' => 'TestFooDate')),
				'preg:/[^<]+/',
				array('input' => array('name' => 'data[Test][fooTime]', 'type' => 'text', 'tabindex' => 2, 'class' => 'time', 'id' => 'TestFooTime')),
			'/div'
		);
		$this->assertTags($result, $expected);
	}

	// Maybe testing HABTM output

	// Maybe testing belongsTo output

	// Maybe testing Plain output

	// Required input
	function testInputRequired() {
		$result = $this->helper->input('Test.foo', array('required' => true));
		$expected = array(
			'div' => array('class' => 'input required'),
				'label' => array('for' => 'TestFoo'),
					'Foo',
				'/label',
				'input' => array(
					'name' => 'data[Test][foo]',
					'type' => 'text',
					'tabindex' => 1,
					'id' => 'TestFoo'
				),
				'span' => array('class' => 'icon iconRequired'),
				'/span',
			'/div'
		);
		$this->assertTags($result, $expected);
	}

	// Required tooltip help
	function testInputHelp() {
		$result = $this->helper->input('Test.foo', array('help' => 'Bar'));
		$expected = array(
			'div' => array('class' => 'input'),
				'label' => array('for' => 'TestFoo'),
					'Foo',
				'/label',
				'input' => array(
					'name' => 'data[Test][foo]',
					'type' => 'text',
					'tabindex' => 1,
					'id' => 'TestFoo'
				),
				array('div' => array('class' => 'help')),
					array('span' => array('class' => 'icon iconTooltip')),
					'/span',
					array('span' => array('class' => 'tooltip')),
						'Bar',
					'/span',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);
	}


	// Displaying a document_ input will trigger the DocumentHelper->input method
	function testInputDocument() {
		Mock::generatePartial('DocumentHelper', 'MockDocumentHelperInput', array('input'));
		$this->helper->Document = new MockDocumentHelperInput();
		$this->helper->Document->setReturnValue('input', 'bar');
		$result = $this->helper->input('Text.document_foo');
		$this->assertEqual($result, 'bar');
	}


}
?>