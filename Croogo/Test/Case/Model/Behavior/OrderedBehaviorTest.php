<?php

App::uses('CroogoTestCase', 'Croogo.TestSuite');

class OrderedBehaviorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.croogo.order_record'
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->OrderRecord = ClassRegistry::init('OrderRecord');
		$this->OrderRecord->Behaviors->attach('Ordered', array('field' => 'weight', 'foreign_key' => null));
		$this->defaultPrefix = Configure::read('Cache.defaultPrefix');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->OrderRecord);
		ClassRegistry::flush();
	}

	public function testNewRecordAddedShouldHaveAWeightSet() {
		$data = array(
			'id' => '',
			'title' => 'Some title',
		);

		$saved = $this->OrderRecord->save($data);
		$this->assertTrue((bool)$saved);

		$latestRecord = $this->OrderRecord->findById(2);
		$this->assertNotNull($latestRecord['OrderRecord']['weight']);
	}

}
