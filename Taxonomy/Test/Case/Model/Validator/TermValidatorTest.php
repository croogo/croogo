<?php
App::uses('TaxonomyAppValidator', 'Taxonomy.Model/Validator');
App::uses('TermValidator', 'Taxonomy.Model/Validator');
App::uses('Term', 'Taxonomy.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class TermValidatorTest extends CroogoTestCase {
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.taxonomy.term',
		'plugin.translate.i18n',
	);

	public $Validator;
	private $_record;

	public function setUp() {
		parent::setUp();
		$Fixtures = new TermFixture();
		$this->_record = $Fixtures->records[0];
		$this->Validator = new TermValidator(ClassRegistry::init('Taxonomy.Term'));

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

	public function testSlugCannotBeEmpty() {
		$this->Validator->getModel()->set('slug', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('slug', $errors);
	}

	public function testSlugShouldBeUnique() {
		$this->Validator->getModel()->set('slug', 'mytag');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('slug', $errors);
	}
}
