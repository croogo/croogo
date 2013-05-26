<?php
App::uses('Taxonomy', 'Taxonomy.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class TaxonomyTest extends CroogoTestCase {

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
		'plugin.taxonomy.nodes_taxonomy',
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
		$this->assertEquals(1, $this->Taxonomy->termInVocabulary(1, 1)); // Uncategorized in Categories
		$this->assertFalse($this->Taxonomy->termInVocabulary(1, 3)); // Uncategorized in non-existing vocabulary
	}
}
