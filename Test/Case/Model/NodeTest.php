<?php
App::uses('Node', 'Model');
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
		$this->Node->__cacheTerms();
		$this->assertEqual($this->Node->data['Node']['terms'], '{"1":"uncategorized","2":"announcements"}');
	}
}
