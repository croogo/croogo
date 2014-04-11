<?php
App::uses('NodeValidator', 'Nodes.Model/Validator');
App::uses('Node', 'Nodes.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class NodeValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.nodes.node',
		'plugin.translate.i18n',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new NodeFixture();
		$this->_record = $Fixtures->records[1];
		$this->Validator = new NodeValidator(ClassRegistry::init('Nodes.Node'));

		$this->Validator->getModel()->set($this->_record);
	}

	public function tearDown() {
		unset($this->Validator);
		parent::tearDown();
	}

	public function testRecordShouldBeValid() {
		$errors = $this->Validator->errors();
		$this->assertEmpty($errors);
	}

	public function testTitleCannotBeEmpty() {
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

	public function testSlugCannotBeEmpty() {
		$this->Validator->getModel()->set('slug', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('slug', $errors);
	}

	public function testSlugShouldBeUniquePerType() {
		$this->Validator->getModel()->set('slug', 'protected');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('slug', $errors);
	}

}
