<?php
App::uses('Node', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

class CroogoTranslateBehaviorTest extends CroogoTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
		'plugin.blocks.block',
		'comment',
		'contact',
		'i18n',
		'language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'message',
		'meta',
		'node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'setting',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.term',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.users.user',
		'plugin.taxonomy.vocabulary',
	);

	public $Node = null;

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Node = ClassRegistry::init('Node');
		$this->Node->Behaviors->attach('CroogoTranslate', array(
			'title' => 'titleTranslation',
		));
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Node);
		ClassRegistry::flush();
	}

	public function testSaveTranslation() {
		$this->Node->id = 2; // About
		$this->Node->locale = 'ben';
		$this->Node->saveTranslation(array(
			'Node' => array(
				'title' => 'About [Translated in Bengali]',
			),
		));
		$about = $this->Node->findById('2');
		$this->assertEqual($about['Node']['title'], 'About [Translated in Bengali]');
	}

}
