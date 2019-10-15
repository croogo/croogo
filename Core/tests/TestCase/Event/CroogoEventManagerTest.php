<?php

namespace Croogo\Core\Test\TestCase\Event;

use Cake\Cache\Cache;
use Cake\Controller\Component\AuthComponent;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\View\View;
use Croogo\Core\Croogo;
use Croogo\Core\Event\EventManager;
use Croogo\Core\PluginManager;
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
        'plugin.Croogo/Core.Settings',
    ];

    public function setUp()
    {
        parent::setUp();

        PluginManager::unload('Example');
        PluginManager::load('Shops', ['events' => true, 'autoload' => true]);
        PluginManager::events();
        EventManager::loadListeners();
        $request = new ServerRequest();
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

        PluginManager::unload('Shops');
    }

    /**
     * Indirectly test EventManager::detachPluginSubscribers()
     * triggerred by calling PluginManager::unload(null)
     */
    public function testDetachPluginSubscribers()
    {
        $loaded = Plugin::isLoaded('Shops');
        $this->assertNotEmpty($loaded);

        $eventName = 'Controller.Users.activationFailure';
        $event = Croogo::dispatchEvent($eventName, $this->Users);
        $this->assertTrue($event->result, sprintf('Event: %s', $eventName));

        PluginManager::unload('Shops');

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
            $this->assertInstanceOf(
                '\\Croogo\\Core\\Test\\TestCase\\Event\\TestUsersEventController',
                $event->getSubject()
            );
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
            $this->assertInstanceOf(
                '\\Croogo\\Core\\Test\\TestCase\\Event\\TestNodesEventController',
                $event->getSubject()
            );
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
            $this->assertInstanceOf('\\Cake\\View\\View', $event->getSubject());
        }
    }
}
