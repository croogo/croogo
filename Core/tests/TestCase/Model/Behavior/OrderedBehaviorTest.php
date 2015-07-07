<?php

namespace Croogo\Core\Test\TestCase\Model\Behavior;

use Cake\Core\Configure;
use Croogo\Core\Test\Fixture\OrderRecordFixture;
use Croogo\Core\TestSuite\CroogoTestCase;

class OrderedBehaviorTest extends CroogoTestCase {

/**
 * Fixtures
 */
	public $fixtures = array(
		'plugin.Croogo/Core.order_record',
//		'plugin.blocks.block',
//		'plugin.blocks.region',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
//		$this->OrderRecord = ClassRegistry::init('OrderRecord');
//		$this->OrderRecord->Behaviors->attach('Ordered', array('field' => 'weight', 'foreign_key' => null));
		$this->defaultPrefix = Configure::read('Cache.defaultPrefix');
//		$this->Block = ClassRegistry::init('Blocks.Block');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->OrderRecord);
//		ClassRegistry::flush();
	}

	public function testNewRecordAddedShouldHaveAWeightSet() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$data = array(
			'id' => '',
			'title' => 'Some title',
		);

		$saved = $this->OrderRecord->save($data);
		$this->assertTrue((bool)$saved);

		$latestRecord = $this->OrderRecord->findById(2);
		$this->assertNotNull($latestRecord['OrderRecord']['weight']);
	}

/**
 * testWeightMustBeSet
 */
	public function testWeightMustBeSet() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

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
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

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
