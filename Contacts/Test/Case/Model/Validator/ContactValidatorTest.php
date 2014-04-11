<?php
App::uses('ContactsAppValidator', 'Contacts.Model/Validator');
App::uses('ContactValidator', 'Contacts.Model/Validator');
App::uses('Contact', 'Contacts.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class ContactValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.translate.i18n',
		'plugin.contacts.contact',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new ContactFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new ContactValidator(ClassRegistry::init('Contacts.Contact'));

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

	public function testAliasCannotBeEmpty() {
		$this->Validator->getModel()->set('alias', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testAliasShouldBeUnique() {
		$this->Validator->getModel()->set('alias', 'contact-2');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}
}
