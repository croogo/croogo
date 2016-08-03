<?php
namespace Croogo\Core\Test\TestCase\Event;

use Cake\Cache\Cache;
use Cake\Controller\Component\AuthComponent;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\View\View;
use Croogo\Core\Croogo;
use Croogo\Core\Event\EventManager;
use Croogo\Core\Plugin;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Core\TestSuite\TestCase;
use Croogo\Nodes\Controller\Admin\NodesController;
use Croogo\Users\Controller\Admin\UsersController;

class TestUsersEventController extends UsersController
{
}

class TestNodesEventController extends NodesController
{
}

class EventManagerTest extends TestCase
{

    public $fixtures = [
        'plugin.croogo/core.settings',
    ];

    public function setUp()
    {
        parent::setUp();

        Plugin::unload('Example');
        Plugin::load('Shops', ['events' => true, 'autoload' => true]);
        Plugin::events();
        EventManager::loadListeners();
        $request = new Request();
        $response = new Response();
        $this->Users = new TestUsersEventController($request, $response);
        $this->Nodes = new TestNodesEventController($request, $response);
    }

/**
 * tearDown
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();

        Plugin::unload('Shops');
    }

/**
 * Indirectly test EventManager::detachPluginSubscribers()
 * triggerred by calling Plugin::unload(null)
 */
    public function testDetachPluginSubscribers()
    {
        $loaded = Plugin::loaded('Shops');
        $this->assertNotEmpty($loaded);

        $eventName = 'Controller.Users.activationFailure';
        $event = Croogo::dispatchEvent($eventName, $this->Users);
        $this->assertTrue($event->result, sprintf('Event: %s', $eventName));

        Plugin::unload('Shops');

        $eventName = 'Controller.Users.activationFailure';
        $event = Croogo::dispatchEvent($eventName, $this->Users);
        $this->assertNull($event->result, sprintf('Event: %s', $eventName));
    }

/**
 * Test Reuse the same Event Listener class
 */
    public function testAliasingEventListener()
    {
        $eventManager = EventManager::instance();
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
        EventManager::loadListeners();

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
        $Auth = $this->getMockBuilder(AuthComponent::class)
            ->setConstructorArgs([$this->Users->components()])
            ->getMock();
        $Auth->authenticate = [
            'all' => [
                'userModel' => 'Users',
                'fields' => ['username' => 'username', 'password' => 'password'],
            ],
        ];
        $this->Users->Auth = $Auth;
        foreach ($eventNames as $name) {
            $event = Croogo::dispatchEvent($name, $this->Users);
            $this->assertTrue($event->result, sprintf('Event: %s', $name));
            $this->assertInstanceOf('\\Croogo\\Core\\Test\\TestCase\\Event\\TestUsersEventController', $event->subject());
        }
    }

/**
 * testDispatchNodesEvents
 */
    public function testDispatchNodesEvents()
    {
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
            $this->assertInstanceOf('\\Croogo\\Core\\Test\\TestCase\\Event\\TestNodesEventController', $event->subject());
        }
    }

/**
 * testDispatchHelperEvents
 */
    public function testDispatchHelperEvents()
    {
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
