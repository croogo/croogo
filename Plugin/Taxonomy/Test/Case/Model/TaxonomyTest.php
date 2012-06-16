<?php
App::uses('Taxonomy', 'Taxonomy.Model');
App::uses('CroogoTestCase', 'TestSuite');

class TaxonomyTest extends CroogoTestCase {

	public $fixtures = array(
		'app.aco',
		'app.aro',
		'app.aros_aco',
		'plugin.blocks.block',
		'app.comment',
		'app.contact',
		'app.i18n',
		'app.language',
		'app.link',
		'app.menu',
		'app.message',
		'app.meta',
		'app.node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'app.role',
		'app.setting',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.term',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'app.user',
		'plugin.taxonomy.vocabulary',
	);

	public function setUp() {
		parent::setUp();
		$this->Taxonomy = ClassRegistry::init('Taxonomy.Taxonomy');
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
