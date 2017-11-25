<?php

namespace Croogo\Core\Test\TestCase\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Croogo\Core\Controller\Component\BaseApiComponent;
use Croogo\Core\TestSuite\CroogoTestCase;

class TestApiComponent extends BaseApiComponent
{

    protected $_apiVersion = 'v1.0';

    protected $_apiMethods = [
        'index',
        'view',
        'delete',
    ];
}

class TestBaseApiController extends Controller
{

    public function initialize()
    {
        $this->components()->set('TestApi', new TestApiComponent($this->components()));
    }


    public function index()
    {
    }

    public function view()
    {
    }
}

class BaseApiComponentTest extends CroogoTestCase
{

    public $fixtures = [
//		'plugin.croogo/settings.setting',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->markTestSkipped('This hasn\'t been ported yet');

        $request = $this->_apiRequest([
            'api' => 'api', 'prefix' => 'v1.0',
            'controller' => 'users', 'action' => 'index',
        ]);
        $response = $this->getMock('\\Cake\\Network\\Response');

        $this->Controller = new TestBaseApiController($request, $response);
//		$this->Controller->constructClasses();
        $this->Controller->startupProcess();
        $this->TestApi = $this->Controller->TestApi;
    }

    public function testControllerMethodInjection()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $expected = [
            'index', 'view', 'v1_0_index', 'v1_0_view', 'v1_0_delete',
        ];
        $this->assertEquals($expected, $this->Controller->methods);
    }

    public function testVersion()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->assertEquals('v1.0', $this->TestApi->version());
    }

    public function testApiMethods()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $expected = ['index', 'view', 'delete'];
        $this->assertEquals($expected, $this->TestApi->apiMethods());
    }

    public function testIsValidAction()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->assertEquals(true, $this->TestApi->isValidAction('index'));
        $this->assertEquals(false, $this->TestApi->isValidAction('bogus'));
    }
}
