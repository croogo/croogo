<?php
App::uses('MenusAppValidator', 'Menus.Model/Validator');
App::uses('Menus', 'Menus.Model');
App::uses('Link', 'Menus.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class MenusAppValidatorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.menus.menu',
		'plugin.translate.i18n',
		'plugin.menus.link',
	);

	public $Validator;

	public function tearDown() {
		unset($this->Validator);
		parent::tearDown();
	}

	public function testMenusTitleCannotBeEmpty() {
		$this->Validator = new MenusAppValidator(ClassRegistry::init('Menus.Menus'));
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

	public function testLinkTitleCannotBeEmpty() {
		$this->Validator = new MenusAppValidator(ClassRegistry::init('Menus.Link'));
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

}
