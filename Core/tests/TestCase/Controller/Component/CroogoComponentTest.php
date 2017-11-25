<?php

namespace Croogo\Core\Test\TestCase\Controller\Component;

use App\Controller\AppController;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Croogo\Core\Controller\Component\CroogoComponent;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\TestSuite\TestCase;

class MockCroogoComponent extends CroogoComponent
{

    public function startup(Event $event)
    {
        $this->_controller = $event->subject();
    }
}

class CroogoComponentTest extends TestCase
{

    public $fixtures = [
//		'plugin.croogo/users.aco',
//		'plugin.croogo/users.aro',
//		'plugin.croogo/users.aros_aco',
//		'plugin.croogo/settings.setting',
//		'plugin.croogo/menus.menu',
//		'plugin.croogo/menus.link',
//		'plugin.croogo/users.role',
//		'plugin.croogo/taxonomy.type',n
//		'plugin.croogo/taxonomy.vocabulary',
//		'plugin.croogo/taxonomy.types_vocabulary',
//		'plugin.croogo/nodes.node',
    ];

    public $component = null;
    public $controller = null;

    public function setUp()
    {
        parent::setUp();

        // Setup our component and fake test controller
        $this->controller = $this->getMockBuilder(Controller::class)
            ->setMethods(['redirect'])
            ->setConstructorArgs([new Request, new Response])
            ->getMock();

        $registry = new ComponentRegistry($this->controller);
        $this->component = new CroogoComponent($registry);

//		$this->Controller = new Controller(new Request(), new Response());
////		$this->Controller->constructClasses();
//		$this->Controller->Croogo = new MockCroogoComponent($this->Controller->components());
//		$this->Controller->components()->unload('Blocks');
//		$this->Controller->components()->unload('Menus');
//		$this->Controller->components()->set('Croogo', $this->Controller->Croogo);
//		$this->Controller->startupProcess();
    }

    public function testAddRemoveAcos()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $Aco = ClassRegistry::init('Aco');

        $this->Controller->Croogo->addAco('CroogoTestController');
        $parent = $Aco->findByAlias('CroogoTestController');
        $this->assertNotEmpty($parent);

        $this->Controller->Croogo->addAco('CroogoTestController/index');
        $child = $Aco->findByParentId($parent['Aco']['id']);
        $this->assertNotEmpty($child);

        $this->Controller->Croogo->removeAco('CroogoTestController/index');
        $child = $Aco->findByParentId($parent['Aco']['id']);
        $this->assertEmpty($child);

        $this->Controller->Croogo->removeAco('CroogoTestController');
        $parent = $Aco->findByAlias('CroogoTestController');
        $this->assertEmpty($parent);
    }

/**
 * testRedirect
 *
 * @return void
 * @dataProvider redirectData
 */
    public function testRedirect($expected, $url, $data = [], $indexUrl = [])
    {
        $this->controller->request->data = $data;
        $this->controller->expects($this->once())
            ->method('redirect')
            ->with($this->equalTo($expected));
        $CroogoComponent = new CroogoComponent(new ComponentRegistry());
        $CroogoComponent->startup(new Event(null, $this->controller));
        $CroogoComponent->redirect($url, null, true, $indexUrl);
    }

/**
 * redirectData
 *
 * @return array
 */
    public function redirectData()
    {
        return [
            ['croogo.org', 'croogo.org'],
            [['action' => 'index'], ['action' => 'edit', 1]],
            [['action' => 'edit', 1], ['action' => 'edit', 1], ['apply' => 'Apply']],
            [['action' => 'index', 1], ['action' => 'edit', 1], [], ['action' => 'index', 1]],
            [['action' => 'edit', 1], ['action' => 'edit', 1], ['apply' => 'Apply'], ['action' => 'index', 1]],
        ];
    }

    public function tearDown()
    {
        parent::tearDown();

        // Clean up after we're done
        unset($this->component, $this->controller);
    }
}
