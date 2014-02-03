<?php

App::uses('CroogoStatus', 'Croogo.Lib');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoStatusTest extends CroogoTestCase implements CakeEventListener {

	public function implementedEvents() {
		return array(
			'Croogo.Status.setup' => array(
				'callable' => 'onCroogoStatusSetup',
			),
		);
	}

/**
 * onCroogoStatusSetup
 */
	public function onCroogoStatusSetup($event) {
		$event->data['publishing'][4] = 'Added by event handler';
	}

/**
 * setUp
 */
	public function setUp() {
		CakeEventManager::instance()->attach($this);
		$this->CroogoStatus = new CroogoStatus();
	}

/**
 * tearDown
 */
	public function tearDown() {
		CakeEventManager::instance()->detach($this);
		unset($this->CroogoStatus);
	}

/**
 * testByDescription
 */
	public function testByDescription() {
		$result = $this->CroogoStatus->byDescription('Published');
		$this->assertEquals(1, $result);
	}

/**
 * testById
 */
	public function testById() {
		$result = $this->CroogoStatus->byId(2);
		$this->assertEquals('Preview', $result);
	}

/**
 * testStatuses
 */
	public function testStatuses() {
		$result = $this->CroogoStatus->statuses();
		$this->assertTrue(count($result) >= 4);
	}

/**
 * testStatus
 */
	public function testStatus() {
		$expected = array(CroogoStatus::PUBLISHED);
		$result = $this->CroogoStatus->status();
		$this->assertEquals($expected, $result);
	}

/**
 * modifyStatus callback
 */
	public function modifyStatus($event) {
		switch ($event->data['accessType']) {
			case 'webmaster':
				if (!in_array(CroogoStatus::PREVIEW, $event->data['values'])) {
					$event->data['values'][] = CroogoStatus::PREVIEW;
				}
			break;
			default:
				$event->data['values'] = array(null);
			break;
		}
	}

/**
 * testStatusModifiedByEventHandler
 */
	public function testStatusModifiedByEventHandler() {
		$callback = array($this, 'modifyStatus');
		CakeEventManager::instance()->detach($this);
		CakeEventManager::instance()->attach($callback, 'Croogo.Status.status');

		// test status is modified for 'webmaster' type by event handler
		$expected = array(CroogoStatus::PUBLISHED, CroogoStatus::PREVIEW);
		$this->CroogoStatus = new CroogoStatus();
		$result = $this->CroogoStatus->status('publishing', 'webmaster');
		$this->assertEquals($expected, $result);

		// test status is emptied for unknown type
		$expected = array(null);
		$result = $this->CroogoStatus->status('publishing', 'bogus');
		$this->assertEquals($expected, $result);

		CakeEventManager::instance()->detach($callback, 'Croogo.Status.status');
	}

/**
 * testArrayAccessUsage
 */
	public function testArrayAccessUsage() {
		$newIndex = 5;
		$count = count($this->CroogoStatus->statuses());
		$this->CroogoStatus['publishing'][$newIndex] = 'New status';
		$this->assertEquals($count + 1, count($this->CroogoStatus->statuses()));
		unset($this->CroogoStatus['publishing'][$newIndex]);
		$this->assertEquals($count, count($this->CroogoStatus->statuses()));
		$this->assertFalse(isset($this->CroogoStatus['publishing'][$newIndex]));
	}

}
