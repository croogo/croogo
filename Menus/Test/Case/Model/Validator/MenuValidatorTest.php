<?php
App::uses('MenusAppValidator', 'Menus.Model/Validator');
App::uses('MenuValidator', 'Menus.Model/Validator');
App::uses('Menu', 'Menus.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class MenuValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.translate.i18n',
		'plugin.menus.menu',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new MenuFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new MenuValidator(ClassRegistry::init('Menus.Menu'));

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
		$this->Validator->getModel()->set('alias', 'footer');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}
}
