<?php

namespace Croogo\Core\Test\TestCase;

use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Croogo\Core\Status;
use Croogo\Core\TestSuite\CroogoTestCase;

class CroogoStatusTest extends CroogoTestCase implements EventListenerInterface
{
    public function implementedEvents()
    {
        return [
            'Croogo.Status.setup' => [
                'callable' => 'onCroogoStatusSetup',
            ],
        ];
    }

/**
 * onCroogoStatusSetup
 */
    public function onCroogoStatusSetup($event)
    {
        $event->data['publishing'][4] = 'Added by event handler';
    }

/**
 * setUp
 */
    public function setUp()
    {
        EventManager::instance()->attach($this);
        $this->CroogoStatus = new Status();
    }

/**
 * tearDown
 */
    public function tearDown()
    {
        EventManager::instance()->detach($this);
        unset($this->CroogoStatus);
    }

/**
 * testByDescription
 */
    public function testByDescription()
    {
        $result = $this->CroogoStatus->byDescription('Published');
        $this->assertEquals(1, $result);
    }

/**
 * testById
 */
    public function testById()
    {
        $result = $this->CroogoStatus->byId(2);
        $this->assertEquals('Preview', $result);
    }

/**
 * testStatuses
 */
    public function testStatuses()
    {
        $result = $this->CroogoStatus->statuses();
        $this->assertTrue(count($result) >= 4);
    }

/**
 * testStatus
 */
    public function testStatus()
    {
        $expected = [Status::PUBLISHED];
        $result = $this->CroogoStatus->status();
        $this->assertEquals($expected, $result);
    }

/**
 * modifyStatus callback
 */
    public function modifyStatus($event)
    {
        switch ($event->data['accessType']) {
            case 'webmaster':
                if (!in_array(Status::PREVIEW, $event->data['values'])) {
                    $event->data['values'][] = Status::PREVIEW;
                }
                break;
            default:
                $event->data['values'] = [null];
                break;
        }
    }

/**
 * testStatusModifiedByEventHandler
 */
    public function testStatusModifiedByEventHandler()
    {
        $callback = [$this, 'modifyStatus'];
        EventManager::instance()->on($this);
        EventManager::instance()->on('Croogo.Status.status', $callback);

        // test status is modified for 'webmaster' type by event handler
        $expected = [Status::PUBLISHED, Status::PREVIEW];
        $this->CroogoStatus = new Status();
        $result = $this->CroogoStatus->status(1, 'publishing', 'webmaster');
        $this->assertEquals($expected, $result);

        // test status is emptied for unknown type
        $expected = [null];
        $result = $this->CroogoStatus->status(1, 'publishing', 'bogus');
        $this->assertEquals($expected, $result);

        EventManager::instance()->on('Croogo.Status.status', $callback);
    }

/**
 * testArrayAccessUsage
 */
    public function testArrayAccessUsage()
    {
        $newIndex = 5;
        $count = count($this->CroogoStatus->statuses());
        $this->CroogoStatus['publishing'][$newIndex] = 'New status';
        $this->assertEquals($count + 1, count($this->CroogoStatus->statuses()));
        unset($this->CroogoStatus['publishing'][$newIndex]);
        $this->assertEquals($count, count($this->CroogoStatus->statuses()));
        $this->assertFalse(isset($this->CroogoStatus['publishing'][$newIndex]));
    }
}
