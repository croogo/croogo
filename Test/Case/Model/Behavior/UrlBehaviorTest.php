<?php
App::uses('Node', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

class UrlBehaviorTest extends CroogoTestCase {

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

	public $Node = null;

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Node = ClassRegistry::init('Node');
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
		$this->assertEqual($helloWorld['Node']['url'], array(
			'plugin' => false,
			'controller' => 'nodes',
			'action' => 'view',
			'type' => 'blog',
			'slug' => 'hello-world',
		));
	}

	public function testMultiple() {
		$result = $this->Node->find('all');
		$this->assertEqual($result['0']['Node']['url'], array(
			'plugin' => false,
			'controller' => 'nodes',
			'action' => 'view',
			'type' => $result['0']['Node']['type'],
			'slug' => $result['0']['Node']['slug'],
		));
	}

}
