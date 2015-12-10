<?php
namespace Croogo\Core\Test\TestCase\Event;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\View\View;
use Croogo\Core\Croogo;
use Croogo\Core\Event\CroogoEventManager;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Extensions\CroogoPlugin;

use Croogo\Nodes\Controller\NodesController;
use Croogo\Users\Controller\UsersController;

//class TestUsersEventController extends UsersController {
//}
//
//class TestNodesEventController extends NodesController {
//}

class CroogoEventManagerTest extends CroogoTestCase
{

    public $fixtures = [
//		'plugin.croogo/settings.setting',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->markTestIncomplete('This hasn\'t been ported yet');

        CroogoPlugin::unload('Example');
        CroogoPlugin::load('Shops', ['autoload' => true]);
        CroogoEventManager::loadListeners();
        $request = $this->getMock('\\Cake\\Network\\Request');
        $response = $this->getMock('\\Cake\\Network\\Response');
//		$this->Users = new TestUsersEventController($request, $response);
//		$this->Nodes = new TestNodesEventController($request, $response);
    }

/**
 * tearDown
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();

//		CroogoPlugin::unload('Shops');
    }

/**
 * Indirectly test CroogoEventManager::detachPluginSubscribers()
 * triggerred by calling CroogoPlugin::unload(null)
 */
    public function testDetachPluginSubscribers()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $loaded = Plugin::loaded('Shops');
        $this->assertNotEmpty($loaded);

        $eventName = 'Controller.Users.activationFailure';
        $event = Croogo::dispatchEvent($eventName, $this->Users);
        $this->assertTrue($event->result, sprintf('Event: %s', $eventName));

        CroogoPlugin::unload('Shops');

        $eventName = 'Controller.Users.activationFailure';
        $event = Croogo::dispatchEvent($eventName, $this->Users);
        $this->assertNull($event->result, sprintf('Event: %s', $eventName));
    }

/**
 * Test Reuse the same Event Listener class
 */
    public function testAliasingEventListener()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $eventManager = CroogoEventManager::instance();
        $listeners = $eventManager->listeners('Controller.Nodes.afterAdd');
        foreach ($listeners as $listener) {
            $eventManager->detach($listener['callable']);
        }
        $handlers = [
            'Shops.ShopsNodesEventHandler',
            'CustomShopsNodesEventHandler' => [
                'options' => [
                    'className' => 'Shops.ShopsNodesEventHandler',
                ],
            ],
        ];
        Configure::write('EventHandlers', $handlers);
        Cache::delete('EventHandlers', 'cached_settings');
        CroogoEventManager::loadListeners();

        $listeners = $eventManager->listeners('Controller.Nodes.afterAdd');
        foreach ($listeners as $listener) {
            $this->assertInstanceOf('\\Shops\\Event\\ShopsNodesEventHandler', $listener['callable'][0]);
        }
    }

/**
 * testDispatchUsersEvents
 */
    public function testDispatchUsersEvents()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $eventNames = [
            'Controller.Users.activationFailure',
            'Controller.Users.activationSuccessful',
            'Controller.Users.beforeAdminLogin',
            'Controller.Users.adminLoginFailure',
            'Controller.Users.adminLoginSuccessful',
            'Controller.Users.adminLogoutSuccessful',
            'Controller.Users.afterLogout',
            'Controller.Users.beforeLogin',
            'Controller.Users.beforeLogout',
            'Controller.Users.loginFailure',
            'Controller.Users.loginSuccessful',
            'Controller.Users.registrationFailure',
            'Controller.Users.registrationSuccessful',
        ];
        $Auth = $this->getMock('\\Cake\\Controller\\Component\\AuthComponent', [], [$this->Users->components()]);
        $Auth->authenticate = [
            'all' => [
                'userModel' => 'User',
                'fields' => ['username' => 'username', 'password' => 'password'],
            ],
        ];
        $this->Users->Auth = $Auth;
        foreach ($eventNames as $name) {
            $event = Croogo::dispatchEvent($name, $this->Users);
            $this->assertTrue($event->result, sprintf('Event: %s', $name));
            $this->assertInstanceOf('\\Croogo\\Croogo\\Test\\TestCase\\Event\\TestUsersEventController', $event->subject());
        }
    }

/**
 * testDispatchNodesEvents
 */
    public function testDispatchNodesEvents()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $eventNames = [
            'Controller.Nodes.afterAdd',
            'Controller.Nodes.afterDelete',
            'Controller.Nodes.afterEdit',
            'Controller.Nodes.afterPromote',
            'Controller.Nodes.afterPublish',
            'Controller.Nodes.afterUnpromote',
            'Controller.Nodes.afterUnpublish',
        ];
        foreach ($eventNames as $name) {
            $event = Croogo::dispatchEvent($name, $this->Nodes);
            $this->assertTrue($event->result, sprintf('Event: %s', $name));
            $this->assertInstanceOf('\\Croogo\\Croogo\\Test\\TestCase\\Event\\TestNodesEventController', $event->subject());
        }
    }

/**
 * testDispatchHelperEvents
 */
    public function testDispatchHelperEvents()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $eventNames = [
            'Helper.Layout.afterFilter',
            'Helper.Layout.beforeFilter',
        ];
                $View = new View();
        foreach ($eventNames as $name) {
            $event = Croogo::dispatchEvent($name, $View);
            $this->assertTrue($event->result, sprintf('Event: %s', $name));
            $this->assertInstanceOf('\\Cake\\View\\View', $event->subject());
        }
    }
}
