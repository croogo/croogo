<?php
App::uses('RoleValidator', 'Users.Model/Validator');
App::uses('Role', 'Users.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class RoleValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.users.Role',
	);

	public $Validator;
	protected $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new RoleFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new RoleValidator(ClassRegistry::init('Users.Role'));

		$this->Validator->getModel()->set($this->_record);
	}

	public function tearDown() {
		unset($this->Validator);
		parent::tearDown();
	}

	public function testAliasCannotBeEmpty() {
		$this->Validator->getModel()->set('alias', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testAliasIsUnique() {
		$this->Validator->getModel()->set('alias', 'registered');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testAliasIsAlphaNumeric() {
		$this->Validator->getModel()->set('alias', 'registered&"(!');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testTitleCannotBeEmpty() {
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

	public function testTitleIsAlphaNumeric() {
		$this->Validator->getModel()->set('title', 'Some title registered&"(!');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

}
