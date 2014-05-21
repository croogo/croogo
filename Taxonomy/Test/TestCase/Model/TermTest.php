<?php

namespace Croogo\Taxonomy\Test\TestCase\Model;
App::uses('Term', 'Taxonomy.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class TermTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
		'plugin.blocks.block',
		'plugin.comments.comment',
		'plugin.contacts.contact',
		'plugin.translate.i18n',
		'plugin.settings.language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'plugin.contacts.message',
		'plugin.meta.meta',
		'plugin.nodes.node',
		'plugin.taxonomy.model_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'plugin.settings.setting',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.term',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.users.user',
		'plugin.taxonomy.vocabulary',
	);

	public function setUp() {
		parent::setUp();
		$this->Term = ClassRegistry::init('Taxonomy.Term');
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Term);
	}

	public function testSaveAndGetIdShouldNotCreateNewTermWhenSlugAlreadyExists() {
		$oldCount = $this->Term->find('count');
		$exisitingTermData = array(
			'title' => 'Uncategorized',
			'slug' => 'uncategorized',
			'description' => ''
		);

		$this->Term->saveAndGetId($exisitingTermData);
		$newCount = $this->Term->find('count');

		$this->assertEquals($oldCount, $newCount);
	}

	public function testSaveAndGetIdShouldReturnExistingIdOfTermWhenSlugAlreadyExists() {
		$exisitingTermData = array(
			'title' => 'Uncategorized',
			'slug' => 'uncategorized',
			'description' => ''
		);

		$termId = $this->Term->saveAndGetId($exisitingTermData);
		$expectedId = 1;

		$this->assertEquals($expectedId, $termId);
	}

	public function testSaveAndGetIdShouldReturnNewlyCreatedIdOfTermWhenSlugIsNew() {
		$existingIds = $this->Term->find('all', array('fields' => array('id')));

		$newTermData = array(
			'title' => 'Bazinga',
			'slug' => 'bazinga',
			'description' => ''
		);

		$termId = $this->Term->saveAndGetId($newTermData);

		$this->assertFalse(in_array($termId, $existingIds));
	}

	public function testFindByVocabularyWithNoVocabularyIdShouldTriggerError() {
		$this->setExpectedException('PHPUnit_Framework_error');
		$this->Term->find('byVocabulary');
	}

	public function testFindByVocabularyShouldReturnsTermsOfVocabulary() {
		$terms = $this->Term->find('byVocabulary', array('vocabulary_id' => 1));
		$termIds = Hash::extract($terms, '{n}.Term.id');
		sort($termIds);
		$expectedTermIds = array(1, 2);

		$this->assertEquals($expectedTermIds, $termIds);
	}

	public function testTermIsInVocabularyShouldReturnsTrueIfTermAlreadyInVocabulary() {
		$inVocabulary = $this->Term->isInVocabulary(1, 1);
		$this->assertTrue($inVocabulary);
	}

	public function testAddShouldAddNewTerm() {
		$newTermData = array(
			'Taxonomy' => array('parent_id' => null),
			'Term' => array(
				'title' => 'Bazinga',
				'slug' => 'bazinga',
				'description' => ''
			)
		);

		$this->Term->add($newTermData, 1);
		$newTerm = $this->Term->find('first', array('conditions' => array('slug' => 'bazinga')));

		$this->assertNotEmpty($newTerm);
	}

	public function testHasSlugChangedShouldReturnTrueIfSlugChanged() {
		$changed = $this->Term->hasSlugChanged(1, 'drunk-robot');
		$this->assertTrue($changed);
	}

	public function testHasSlugShouldReturnFalseIfSlugStilltheSame() {
		$changed = $this->Term->hasSlugChanged(1, 'uncategorized');
		$this->assertFalse($changed);
	}

	public function testHasSlugChangedShouldThrowExceptionOnInvalidId() {
		$this->setExpectedException('NotFoundException');
		$this->Term->hasSlugChanged('invalid', 'blah');
	}

	public function testEditShouldReturnTrueWhenRecordSaved() {
		$record = $this->Term->find('first', array('conditions' => array('id' => '1')));
		$record['Taxonomy'] = array('id' => 1, 'parent_id' => null);
		$edited = $this->Term->edit($record, 1);
		$this->assertTrue((bool)$edited);
	}

	public function testEditShouldUpdateRecord() {
		$record = $this->Term->find('first', array('conditions' => array('id' => '1')));
		$record['Term']['slug'] = 'drifting-monkey';
		$record['Taxonomy'] = array('id' => 1, 'parent_id' => null);
		$edited = $this->Term->edit($record, 1);

		$newSlug = $this->Term->field('slug', array('id' => 1));
		$expected = 'drifting-monkey';
		$this->assertEquals($expected, $newSlug);
	}

	public function testRemoveShouldDeleteTerm() {
		$oldCount = $this->Term->find('count');

		$this->Term->remove(1, 1);

		$newCount = $this->Term->find('count');
		$this->assertEquals($oldCount - 1, $newCount);
	}

}
