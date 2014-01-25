<?php
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

}
