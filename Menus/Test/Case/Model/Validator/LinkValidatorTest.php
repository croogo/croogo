<?php
App::uses('MenusAppValidator', 'Menus.Model/Validator');
App::uses('LinkValidator', 'Menus.Model/Validator');
App::uses('Link', 'Menus.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class LinkValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.translate.i18n',
		'plugin.menus.link',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new LinkFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new LinkValidator(ClassRegistry::init('Menus.Link'));

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

	public function testLinkCannotBeEmpty() {
		$this->Validator->getModel()->set('link', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('link', $errors);
	}

}
