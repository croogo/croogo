<?php
App::uses('Node', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

class MetaBehaviorTest extends CroogoTestCase {

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
		$this->assertEqual($this->Node->prepareData($data), array(
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
		));
	}

}
