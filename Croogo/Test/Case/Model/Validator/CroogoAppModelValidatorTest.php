<?php
App::uses('CroogoAppModelValidator', 'Croogo.Model/Validator');
App::uses('User', 'Users.Model');
App::uses('CroogoAppModel', 'Croogo.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoAppModelValidatorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.users.user',
		'plugin.translate.i18n',
	);

	public $Validator;
	public function setUp() {
		parent::setUp();
		$Fixture = new UserFixture();
		$this->Validator = new CroogoAppModelValidator(ClassRegistry::init('Users.User'));
		$this->Validator->getModel()->set($Fixture->records[0]);
	}

	public function tearDown() {
		unset($this->Validator);
		parent::tearDown();
	}

	public function testValidNameShouldReturnFalseOnInvalidName() {
		$valid = $this->Validator->validName(array('name' => 'what%is@this#i*dont!even'));
		$this->assertFalse($valid);
	}

	public function testValidNameShouldReturnTrueOnValidName() {
		$valid = $this->Validator->validName(array('name' => 'Kyle'));
		$this->assertTrue($valid);
	}

	public function testValidAliasShouldReturnFalseOnValidAlias() {
		$valid = $this->Validator->validAlias(array('name' => 'Not an alias'));
		$this->assertFalse($valid);
	}

	public function testValidAliasShouldReturnTrueOnValidAlias() {
		$valid = $this->Validator->validAlias(array('name' => 'Kyle'));
		$this->assertTrue($valid);
	}

}
