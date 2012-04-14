<?php
App::uses('Node', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

class EncoderBehaviorTest extends CroogoTestCase {

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

	public function testEncodeWithoutKeys() {
		$array = array('hello', 'world');
		$encoded = $this->Node->encodeData($array);
		$this->assertEqual($encoded, '["hello","world"]');
	}

	public function testEncodeWithKeys() {
		$array = array(
			'first' => 'hello',
			'second' => 'world',
		);
		$encoded = $this->Node->encodeData($array, array(
			'json' => true,
			'trim' => false,
		));
		$this->assertEqual($encoded, '{"first":"hello","second":"world"}');
	}

	public function testDecodeWithoutKeys() {
		$encoded = '["hello","world"]';
		$array = $this->Node->decodeData($encoded);
		$this->assertEqual($array, array('hello', 'world'));
	}

	public function testDecodeWithKeys() {
		$encoded = '{"first":"hello","second":"world"}';
		$array = $this->Node->decodeData($encoded);
		$this->assertEqual($array, array(
			'first' => 'hello',
			'second' => 'world',
		));
	}

}
