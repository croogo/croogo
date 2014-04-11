<?php
App::uses('ContactsAppValidator', 'Contacts.Model/Validator');
App::uses('Contact', 'Contacts.Model');
App::uses('Message', 'Contacts.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class ContactsAppValidatorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.contacts.contact',
		'plugin.translate.i18n',
		'plugin.contacts.message',
	);

	public $Validator;

	public function tearDown() {
		unset($this->Validator);
		parent::tearDown();
	}

	public function testContactTitleCannotBeEmpty() {
		$this->Validator = new ContactsAppValidator(ClassRegistry::init('Contacts.Contact'));
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

	public function testContactEmailShouldBeValid() {
		$this->Validator = new ContactsAppValidator(ClassRegistry::init('Contacts.Contact'));
		$this->Validator->getModel()->set('email', 'xxxxxxxx@invalid-email');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('email', $errors);
	}

	public function testMessageTitleCannotBeEmpty() {
		$this->Validator = new ContactsAppValidator(ClassRegistry::init('Contacts.Message'));
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

	public function testMessageEmailShouldBeValid() {
		$this->Validator = new ContactsAppValidator(ClassRegistry::init('Contacts.Message'));
		$this->Validator->getModel()->set('email', 'xxxxxxxx@invalid-email');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('email', $errors);
	}

}
