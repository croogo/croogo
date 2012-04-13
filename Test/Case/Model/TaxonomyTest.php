<?php
App::uses('Taxonomy', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

class TaxonomyTest extends CroogoTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
		'block',
		'comment',
		'contact',
		'i18n',
		'language',
		'link',
		'menu',
		'message',
		'meta',
		'node',
		'nodes_taxonomy',
		'region',
		'role',
		'setting',
		'taxonomy',
		'term',
		'type',
		'types_vocabulary',
		'user',
		'vocabulary',
	);

	public function setUp() {
		parent::setUp();
		$this->Taxonomy = ClassRegistry::init('Taxonomy');
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Taxonomy);
	}

	public function testGetTree() {
		$tree = $this->Taxonomy->getTree('categories');
		$expected = array(
			'uncategorized' => 'Uncategorized',
			'announcements' => 'Announcements',
		);
		$this->assertEqual($tree, $expected);
	}

	public function testTermInVocabulary() {
		$this->assertEquals(1, $this->Taxonomy->termInVocabulary(1, 1));  // Uncategorized in Categories
		$this->assertFalse($this->Taxonomy->termInVocabulary(1, 3)); // Uncategorized in non-existing vocabulary
	}
}
