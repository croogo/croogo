<?php
App::uses('CommentValidator', 'Comments.Model/Validator');
App::uses('Comment', 'Comments.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CommentValidatorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.comments.comment',
		'plugin.settings.setting',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new CommentFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new CommentValidator(ClassRegistry::init('Comments.Comment'));

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

	public function testNameCannotBeEmpty() {
		$this->Validator->getModel()->set('name', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('name', $errors);
	}

	public function testEmailShouldBeValid() {
		$this->Validator->getModel()->set('email', 'xxxxxxxx@invalid-email');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('email', $errors);
	}
}
