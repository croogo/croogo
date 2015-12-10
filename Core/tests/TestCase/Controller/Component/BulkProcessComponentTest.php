<?php

namespace Croogo\Core\Test\TestCase\Controller\Component;

use Cake\Controller\Controller;
use Cake\Network\Request;
use Croogo\Core\TestSuite\CroogoTestCase;

class BulkProcessComponentTest extends CroogoTestCase
{

    public $setupSettings = false;

    protected function _createController($data)
    {
        $request = new Request();
        $request->data = $data;
        $controller = new Controller($request);
        $controller->loadComponent('Croogo/Core.BulkProcess');
        $controller->startupProcess();
        return $controller;
    }

/**
 * Test that presence of `action` does not affect result
 */
    public function testGetRequestVarsDoNotCountActionAsId()
    {
        $controller = $this->_createController([
            'Node' => [
                'action' => 'copy',
                1 => ['id' => 0],
                2 => ['id' => 1],
            ],
        ]);
        $BulkProcess = $controller->BulkProcess;
        list($action, $ids) = $BulkProcess->getRequestVars('Node');
        $this->assertEquals('copy', $action);
        $this->assertCount(1, $ids);
    }

/**
 * Test that presence of `checkAll` does not affect result
 */
    public function testGetRequestVarsWithCheckallData()
    {
        $controller = $this->_createController([
            'Node' => [
                'checkAll' => 1,
                'action' => 'publish',
                1 => ['id' => 1],
                2 => ['id' => 1],
                3 => ['id' => 3],
            ],
        ]);
        $BulkProcess = $controller->BulkProcess;
        list($action, $ids) = $BulkProcess->getRequestVars('Node');
        $this->assertEquals('publish', $action);
        $this->assertCount(3, $ids);
    }
}
