<?php
App::uses('SettingValidator', '.Model/Validator');
App::uses('Setting', 'Settings.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class SettingValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new SettingFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new SettingValidator(ClassRegistry::init('Settings.Setting'));

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

	public function testKeyCannotBeEmpty() {
		$this->Validator->getModel()->set('key', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('key', $errors);
	}

	public function testKeyShouldBeUnique() {
		$this->Validator->getModel()->set('key', 'Site.tagline');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('key', $errors);
	}

}
