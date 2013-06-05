<?php
App::uses('Node', 'Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoTranslateBehaviorTest extends CroogoTestCase {

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
		'plugin.nodes.node',
		'plugin.meta.meta',
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
		'plugin.translate.i18n',
	);

	public $Node = null;

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Node = ClassRegistry::init('Nodes.Node');
		$this->Node->Behaviors->attach('Translate.CroogoTranslate', array(
			'fields' => array(
				'title' => 'titleTranslation',
			),
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

/**
 * testSaveTranslation
 *
 * @return void
 */
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
