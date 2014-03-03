<?php
App::uses('UserValidator', 'Users.Model/Validator');
App::uses('User', 'Users.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class UserValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.users.user',
	);

	public $Validator;
	protected $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new UserFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new UserValidator(ClassRegistry::init('Users.User'));

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

	public function testUsernameCannotBeEmpty() {
		$this->Validator->getModel()->set('username', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('username', $errors);
	}

	public function testUsernameIsUnique() {
		$this->Validator->getModel()->set('username', 'rchavik');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('username', $errors);
	}

	public function testUsernameIsAlphaNumeric() {
		$this->Validator->getModel()->set('username', 'rchavik@@#&é"(');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('username', $errors);
	}

	public function testEmailIsValid() {
		$this->Validator->getModel()->set('email', 'rchavik@invalid-email');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('email', $errors);
	}

	public function testEmailIsUnique() {
		$this->Validator->getModel()->set('email', 'me@your-site.com');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('email', $errors);
	}

	public function testPasswordMinLength() {
		$this->Validator->getModel()->set('password', 'pass');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('password', $errors);
	}

	public function testVerifyPassword() {
		$this->Validator->getModel()->set('password', 'password1');
		$this->Validator->getModel()->set('verify_password', 'password2');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('verify_password', $errors);
	}

	public function testNameCannotBeEmpty() {
		$this->Validator->getModel()->set('name', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('name', $errors);
	}

	public function testNameIsValid() {
		$this->Validator->getModel()->set('name', 'R@chman Chav!k');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('name', $errors);
	}

	public function testWebsiteIsValid() {
		$this->Validator->getModel()->set('website', '/dev/null');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('website', $errors);
	}

}
