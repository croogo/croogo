<?php
App::uses('TaxonomyAppValidator', 'Taxonomy.Model/Validator');
App::uses('Vocabulary', 'Taxonomy.Model');
App::uses('Type', 'Taxonomy.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class TaxonomyAppValidatorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.taxonomy.vocabulary',
		'plugin.taxonomy.type',
	);

	public $Validator;

	public function tearDown() {
		unset($this->Validator);
		parent::tearDown();
	}

	public function testVocabularyTitleCannotBeEmpty() {
		$this->Validator = new TaxonomyAppValidator(ClassRegistry::init('Taxonomy.Vocabulary'));
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

	public function testVocabularyAliasCannotBeEmpty() {
		$this->Validator = new TaxonomyAppValidator(ClassRegistry::init('Taxonomy.Vocabulary'));
		$this->Validator->getModel()->set('alias', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testVocabularyAliasMustBeUnique() {
		$this->Validator = new TaxonomyAppValidator(ClassRegistry::init('Taxonomy.Vocabulary'));
		$this->Validator->getModel()->set('alias', 'tags');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testTypeTitleCannotBeEmpty() {
		$this->Validator = new TaxonomyAppValidator(ClassRegistry::init('Taxonomy.Type'));
		$this->Validator->getModel()->set('title', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('title', $errors);
	}

	public function testTypeAliasCannotBeEmpty() {
		$this->Validator = new TaxonomyAppValidator(ClassRegistry::init('Taxonomy.Type'));
		$this->Validator->getModel()->set('alias', '');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

	public function testTypeAliasMustBeUnique() {
		$this->Validator = new TaxonomyAppValidator(ClassRegistry::init('Taxonomy.Type'));
		$this->Validator->getModel()->set('alias', 'node');
		$errors = $this->Validator->errors();
		$this->assertArrayHasKey('alias', $errors);
	}

}
