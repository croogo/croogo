<?php
App::uses('ContactsAppValidator', 'Contacts.Model/Validator');
App::uses('MessageValidator', 'Contacts.Model/Validator');
App::uses('Message', 'Contacts.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class MessageValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.contacts.message',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new MessageFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new MessageValidator(ClassRegistry::init('Contacts.Message'));

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

	public function testNameCannotBeEmpty() {
		$this->Validator->getModel()->set('name', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('name', $errors);
	}

	public function testBodyCannotBeEmpty() {
		$this->Validator->getModel()->set('body', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('body', $errors);
	}
}
