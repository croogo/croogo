<?php
App::uses('Node', 'Nodes.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class MetaBehaviorTest extends CroogoTestCase {

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

	public function testSingle() {
		$helloWorld = $this->Node->findBySlug('hello-world');
		$this->assertEqual($helloWorld['CustomFields']['meta_keywords'], 'key1, key2');
	}

	public function testMultiple() {
		$result = $this->Node->find('all', array(
			'order' => 'Node.id ASC',
		));
		$this->assertEqual($result['0']['CustomFields']['meta_keywords'], 'key1, key2');
	}

	public function testPrepareMeta() {
		$data = array(
			'Meta' => array(
				String::uuid() => array(
					'key' => 'key1',
					'value' => 'value1',
				),
				String::uuid() => array(
					'key' => 'key2',
					'value' => 'value2',
				),
			),
		);
		$this->assertEquals(
			array(
				'Meta' => array(
					'0' => array(
						'key' => 'key1',
						'value' => 'value1',
					),
					'1' => array(
						'key' => 'key2',
						'value' => 'value2',
					),
				),
			),
			$this->Node->prepareData($data)
		);
	}

}
