<?php
App::uses('Node', 'Nodes.Model');
App::uses('CroogoTestCase', 'TestSuite');

class NodeTest extends CroogoTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
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
		$this->Node = ClassRegistry::init('Nodes.Node');
		$this->Node->Behaviors->unload('Acl');
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Node);
	}

	public function testCacheTerms() {
		$this->Node->data = array(
			'Node' => array(),
			'Taxonomy' => array(
				'Taxonomy' => array(1, 2), // uncategorized, and announcements
			),
		);
		$this->Node->cacheTerms();
		$this->assertEqual($this->Node->data['Node']['terms'], '{"1":"uncategorized","2":"announcements"}');
	}

	public function testNodeDeleteDependent() {
		// assert existing count
		$commentCount = $this->Node->Comment->find('count',
			array('conditions' => array('Comment.node_id' => 1))
			);
		$this->assertEquals(2, $commentCount);

		$metaCount = $this->Node->Meta->find('count',
			array('conditions' => array('model' => 'Node', 'foreign_key' => 1))
			);
		$this->assertEquals(1, $metaCount);

		// delete node
		$this->Node->id = 1;
		$this->Node->delete();

		$commentCount = $this->Node->Comment->find('count',
			array('conditions' => array('Comment.node_id' => 1))
			);
		$this->assertEqual(0, $commentCount);

		$metaCount = $this->Node->Meta->find('count',
			array('conditions' => array('model' => 'Node', 'foreign_key' => 1))
			);
		$this->assertEqual(0, $metaCount);
	}

/**
 * test saving node.
 */
	public function testAddNode(){
		$this->Node->Behaviors->disable('Tree');
		$oldNodeCount = $this->Node->find('count');

		$data = array(
			'title' => 'Test Content',
			'slug' => 'test-content',
			'type' => 'blog',
			'token_key' => 1,
			'body' => '',
		);
		$result = $this->Node->add(Node::DEFAULT_TYPE, $data);
		$newNodeCount = $this->Node->find('count');

		$this->assertTrue($result);
		$this->assertTrue($this->Node->Behaviors->enabled('Tree'));
		$this->assertEquals($oldNodeCount + 1, $newNodeCount);
	}

	public function testAddNode_WithTaxonomyData(){
		$oldNodeCount = $this->Node->find('count');

		$data = array(
			'Node' => array(
				'title' => 'Test Content',
				'slug' => 'test-content',
				'type' => 'blog',
				'token_key' => 1,
				'body' => '',
			),
			'TaxonomyData' => array(1 => array(0 => '1', 1 => '2'))
		);
		$result = $this->Node->add(Node::DEFAULT_TYPE, $data);
		$newNodeCount = $this->Node->find('count');

		$this->assertTrue($result);
		$this->assertEquals($oldNodeCount + 1, $newNodeCount);
	}

	public function testAddNode_WithVisibilityRole(){
		$oldNodeCount = $this->Node->find('count');

		$data = array(
			'Node' => array(
				'title' => 'Test Content',
				'slug' => 'test-content',
				'type' => 'blog',
				'token_key' => 1,
				'body' => '',
			),
			'Role' => array('Role' => array('3')) //Public
		);
		$result = $this->Node->add(Node::DEFAULT_TYPE, $data);
		$newNodeCount = $this->Node->find('count');

		$this->assertTrue($result);
		$this->assertEquals($oldNodeCount + 1, $newNodeCount);
	}

	public function testAddNode_WithInvalidNodeType(){
		$this->setExpectedException('InvalidArgumentException');
		$data = array(
			'title' => 'Test Content',
			'slug' => 'test-content',
			'type' => 'invalid',
			'token_key' => 1,
			'body' => '',
		);
		$result = $this->Node->add('invalid', $data);
	}
}
