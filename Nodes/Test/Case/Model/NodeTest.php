<?php
App::uses('Node', 'Nodes.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class NodeTest extends CroogoTestCase {

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
		$this->Node = ClassRegistry::init('Nodes.Node');
		$this->Node->Behaviors->unload('Acl');
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Node);
	}

/**
 * Test before Callbacks.
 */
	public function testBeforeSave() {
		$this->Node->type = 'whut ?';
		$data = array(
			'user_id' => 42,
			'title' => 'Test Content',
			'slug' => 'test-content',
			'token_key' => 1,
			'body' => '',
			'path' => '/no-way'
		);
		$result = $this->Node->save($data);
		$this->assertTrue((bool)$result);
		$this->assertEquals('whut ?', $result['Node']['type']);
	}

	public function testBeforeFind() {
		$this->Node->type = 'blog';
		$node = $this->Node->find('first', array('conditions' => array('DATE(created)' => '2009-12-25'), 'recursive' => -1));
		$this->assertNotEmpty($node);

		$expectedNodeId = 1;

		$this->assertEquals($expectedNodeId, $node['Node']['id']);
		$this->assertEquals('blog', $node['Node']['type']);
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
	public function testAddNode() {
		$this->Node->Behaviors->disable('Tree');
		$this->Node->type = null;
		$oldNodeCount = $this->Node->find('count');

		$data = array(
			'title' => 'Test Content',
			'slug' => 'test-content',
			'type' => 'blog',
			'token_key' => 1,
			'body' => '',
		);
		$result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
		$this->Node->type = null;
		$newNodeCount = $this->Node->find('count');

		$this->assertTrue($result);
		$this->assertTrue($this->Node->Behaviors->enabled('Tree'));
		$this->assertEquals($oldNodeCount + 1, $newNodeCount);
	}

/**
 * testAddNodeWithTaxonomyData
 */
	public function testAddNodeWithTaxonomyData() {
		$this->Node->type = null;
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
		$result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
		$this->Node->type = null;
		$newNodeCount = $this->Node->find('count');

		$this->assertTrue($result);
		$this->assertEquals($oldNodeCount + 1, $newNodeCount);
	}

/**
 * testAddNodeWithVisibilityRole
 */
	public function testAddNodeWithVisibilityRole() {
		$this->Node->type = null;
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
		$result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
		$this->Node->type = null;
		$newNodeCount = $this->Node->find('count');

		$this->assertTrue($result);
		$this->assertEquals($oldNodeCount + 1, $newNodeCount);
	}

/**
 * testAddNodeWithInvalidNodeType
 */
	public function testAddNodeWithInvalidNodeType() {
		$this->setExpectedException('InvalidArgumentException');
		$data = array(
			'title' => 'Test Content',
			'slug' => 'test-content',
			'type' => 'invalid',
			'token_key' => 1,
			'body' => '',
		);
		$result = $this->Node->saveNode($data, 'invalid');
	}

/**
 * Test filtering methods
 */
	public function testFilterNodesByTitle() {
		$filterConditions = $this->Node->filterNodes(array('filter' => 'Hello'));
		$node = $this->Node->find('first', array('conditions' => $filterConditions));

		$this->assertNotEmpty($node);
		$this->assertEquals(1, $node['Node']['id']);
	}

	public function testFilterNodesByBody() {
		$filterConditions = $this->Node->filterNodes(array('filter' => 'example'));
		$node = $this->Node->find('first', array('conditions' => $filterConditions));

		$this->assertNotEmpty($node);
		$this->assertEquals(2, $node['Node']['id']);
	}

	public function testFilterNodesWithoutKeyword() {
		$filterConditions = $this->Node->filterNodes();
		$nodes = $this->Node->find('all', array('conditions' => $filterConditions));

		$this->assertEquals(2, count($nodes));
	}

/**
 * test updateAllNodesPaths
 */
	public function testUpdateAllNodesPaths() {
		$node = $this->Node->findById(1);
		$node['Node']['path'] = 'invalid one';
		$this->assertTrue((bool)$this->Node->save($node));

		CroogoRouter::contentType('blog');
		$this->assertTrue($this->Node->updateAllNodesPaths());
		$node = $this->Node->findById(1);
		$this->assertEquals('/blog/hello-world', $node['Node']['path']);
	}

/**
 * Test find('promoted')
 */
	public function testFindPromoted() {
		$results = $this->Node->find('promoted');
		$expectedId = 1;

		$this->assertEquals(1, count($results));
		$this->assertEquals($expectedId, $results[0]['Node']['id']);
		$this->assertEquals(Node::STATUS_PUBLISHED, $results[0]['Node']['status']);
		$this->assertEquals(Node::STATUS_PROMOTED, $results[0]['Node']['promote']);
	}

/**
 * test processActionDelete
 */
	public function testProcessActionDelete() {
		$ids = array('1','2');

		$success = $this->Node->processAction('delete', $ids);
		$count = $this->Node->find('count');

		$this->assertTrue($success);
		$this->assertEquals(0, $count);
	}

/**
 * test processActionPromote
 */
	public function testProcessActionPromote() {
		$ids = array('1','2');

		$success = $this->Node->processAction('promote', $ids);
		$newRecords = $this->Node->find('all');

		$this->assertTrue($success);
		foreach ($newRecords as $record) {
			$this->assertTrue($record['Node']['promote']);
		}
	}

/**
 * test processActionUnpromote
 */
	public function testProcessActionUnpromote() {
		$ids = array('1','2');

		$success = $this->Node->processAction('unpromote', $ids);
		$newRecords = $this->Node->find('all');

		$this->assertTrue($success);
		foreach ($newRecords as $record) {
			$this->assertFalse($record['Node']['promote']);
		}
	}

/**
 * test processActionPublish
 */
	public function testProcessActionPublish() {
		$ids = array('1','2');

		$success = $this->Node->processAction('publish', $ids);
		$newRecords = $this->Node->find('all');

		$this->assertTrue($success);
		foreach ($newRecords as $record) {
			$this->assertTrue($record['Node']['status']);
		}
	}

/**
 * test processActionUnpublish
 */
	public function testProcessActionUnpublish() {
		$ids = array('1','2');

		$success = $this->Node->processAction('unpublish', $ids);
		$newRecords = $this->Node->find('all');

		$this->assertTrue($success);
		foreach ($newRecords as $record) {
			$this->assertFalse($record['Node']['status']);
		}
	}

/**
 * test processActionInvalidAction
 */
	public function testProcessActionInvalidAction() {
		$this->setExpectedException('InvalidArgumentException');
		$this->Node->processAction('avadakadavra', array(1,2));
	}

/**
 * test processActionWithoutIds
 */
	public function testProcessActionWithoutIds() {
		$this->setExpectedException('InvalidArgumentException');
		$this->Node->processAction('delete', array());
	}

}
