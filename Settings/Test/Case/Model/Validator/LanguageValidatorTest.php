<?php
App::uses('LanguageValidator', 'Settings.Model/Validator');
App::uses('Language', 'Settings.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class LanguageValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.settings.language',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new LanguageFixture();
		$this->_record = $Fixtures->records[1];
		$this->Validator = new LanguageValidator(ClassRegistry::init('Settings.Language'));

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

	public function testAliasCannotBeEmpty() {
		$this->Validator->getModel()->set('alias', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testAliasShouldBeUnique() {
		$this->Validator->getModel()->set('alias', 'eng');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

}
