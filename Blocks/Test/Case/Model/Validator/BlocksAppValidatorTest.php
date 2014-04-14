<?php
App::uses('BlocksAppValidator', 'Blocks.Model/Validator');
App::uses('Block', 'Blocks.Model');
App::uses('Region', 'Blocks.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class BlockValidatorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.blocks.block',
		'plugin.blocks.region',
		'plugin.translate.i18n',
	);

	public $Validator;

	public function tearDown() {
		unset($this->Validator);
		parent::tearDown();
	}

	public function testBlockTitleCannotBeEmpty() {
		$this->Validator = new BlocksAppValidator(ClassRegistry::init('Blocks.Block'));
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

	public function testBlockAliasCannotBeEmpty() {
		$this->Validator = new BlocksAppValidator(ClassRegistry::init('Blocks.Block'));
		$this->Validator->getModel()->set('alias', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testBlockAliasMustBeUnique() {
		$this->Validator = new BlocksAppValidator(ClassRegistry::init('Blocks.Block'));
		$this->Validator->getModel()->set('alias', 'search');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testRegionTitleCannotBeEmpty() {
		$this->Validator = new BlocksAppValidator(ClassRegistry::init('Blocks.Region'));
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

	public function testRegionAliasCannotBeEmpty() {
		$this->Validator = new BlocksAppValidator(ClassRegistry::init('Blocks.Region'));
		$this->Validator->getModel()->set('alias', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testRegionAliasMustBeUnique() {
		$this->Validator = new BlocksAppValidator(ClassRegistry::init('Blocks.Region'));
		$this->Validator->getModel()->set('alias', 'footer');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

}
