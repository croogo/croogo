<?php
App::uses('BlocksAppValidator', 'Blocks.Model/Validator');
App::uses('BlockValidator', 'Blocks.Model/Validator');
App::uses('Block', 'Blocks.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class BlockValidatorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.blocks.block',
		'plugin.translate.i18n',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new BlockFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new BlockValidator(ClassRegistry::init('Blocks.Block'));

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

	public function testBodyCannotBeEmpty() {
		$this->Validator->getModel()->set('body', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('body', $errors);
	}
}
