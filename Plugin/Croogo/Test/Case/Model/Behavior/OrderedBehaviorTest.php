<?php

App::uses('Block', 'Blocks.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('OrderedBehavior', 'Croogo.Model/Behavior');

class OrderedBehaviorTest extends CroogoTestCase {

/**
 * Fixtures
 */
	public $fixtures = array(
		'plugin.blocks.block',
		'plugin.blocks.region',
	);

/**
 * setUp
 */
	public function setUp() {
		$this->Block = ClassRegistry::init('Blocks.Block');
	}

	public function tearDown() {
		ClassRegistry::flush();
	}

/**
 * testWeightMustBeSet
 */
	public function testWeightMustBeSet() {
		$result = $this->Block->save(array(
			'Block' => array(
				'id' => '',
				'title' => 'My block',
				'alias' => 'my-block',
				'body' => 'This is my block',
			),
		));
		$this->assertNotEmpty($result['Block']['weight']);
	}

/**
 * testInsertOnEmptyTable
 */
	public function testInsertOnEmptyTable() {
		$this->Block->deleteAll('1 = 1');
		$count = $this->Block->find('count');
		$this->assertEquals(0, $count);
		$result = $this->Block->save(array(
			'Block' => array(
				'title' => 'My other block',
				'alias' => 'my-other-block',
				'body' => 'This is my other block',
			),
		));
		$this->assertEquals(1, $result['Block']['weight']);
	}

}
