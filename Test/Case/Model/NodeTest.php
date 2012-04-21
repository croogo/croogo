<?php
App::uses('Node', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

class NodeTest extends CroogoTestCase {

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
		$this->Node = ClassRegistry::init('Node');
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

}
