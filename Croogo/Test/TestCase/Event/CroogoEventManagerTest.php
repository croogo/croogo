<?php
namespace Croogo\Croogo\Test\TestCase\Event;
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('UsersController', 'Users.Controller');
App::uses('NodesController', 'Nodes.Controller');

class TestUsersEventController extends UsersController {
}

class TestNodesEventController extends NodesController {
}

class CroogoEventManagerTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
	);

	public function setUp() {
		parent::setUp();
		CroogoPlugin::unload('Example');
		CroogoPlugin::load('Shops');
		CroogoEventManager::loadListeners();
		$request = $this->getMock('CakeRequest');
		$response = $this->getMock('CakeResponse');
		$this->Users = new TestUsersEventController($request, $response);
		$this->Nodes = new TestNodesEventController($request, $response);
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		CroogoPlugin::unload('Shops');
	}

/**
 * Indirectly test CroogoEventManager::detachPluginSubscribers()
 * triggerred by calling CroogoPlugin::unload(null)
 */
	public function testDetachPluginSubscribers() {
		$loaded = CakePlugin::loaded('Shops');
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
	public function testAliasingEventListener() {
		$eventManager = CroogoEventManager::instance();
		$listeners = $eventManager->listeners('Controller.Nodes.afterAdd');
		foreach ($listeners as $listener) {
			$eventManager->detach($listener['callable']);
		}
		$handlers = array(
			'Shops.ShopsNodesEventHandler',
			'CustomShopsNodesEventHandler' => array(
				'options' => array(
					'className' => 'Shops.ShopsNodesEventHandler',
				),
			),
		);
		Configure::write('EventHandlers', $handlers);
		Cache::delete('EventHandlers', 'cached_settings');
		CroogoEventManager::loadListeners();

		$listeners = $eventManager->listeners('Controller.Nodes.afterAdd');
		foreach ($listeners as $listener) {
			$this->assertInstanceOf('ShopsNodesEventHandler', $listener['callable'][0]);
		}
	}

/**
 * testDispatchUsersEvents
 */
	public function testDispatchUsersEvents() {
		$eventNames = array(
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
		);
		$Auth = $this->getMock('AuthComponent', array(), array($this->Users->Components));
		$Auth->authenticate = array(
			'all' => array(
				'userModel' => 'User',
				'fields' => array('username' => 'username', 'password' => 'password'),
			),
		);
		$this->Users->Auth = $Auth;
		foreach ($eventNames as $name) {
			$event = Croogo::dispatchEvent($name, $this->Users);
			$this->assertTrue($event->result, sprintf('Event: %s', $name));
			$this->assertInstanceOf('UsersController', $event->subject());
		}
	}

/**
 * testDispatchNodesEvents
 */
	public function testDispatchNodesEvents() {
		$eventNames = array(
			'Controller.Nodes.afterAdd',
			'Controller.Nodes.afterDelete',
			'Controller.Nodes.afterEdit',
			'Controller.Nodes.afterPromote',
			'Controller.Nodes.afterPublish',
			'Controller.Nodes.afterUnpromote',
			'Controller.Nodes.afterUnpublish',
		);
		foreach ($eventNames as $name) {
			$event = Croogo::dispatchEvent($name, $this->Nodes);
			$this->assertTrue($event->result, sprintf('Event: %s', $name));
			$this->assertInstanceOf('NodesController', $event->subject());
		}
	}

/**
 * testDispatchHelperEvents
 */
	public function testDispatchHelperEvents() {
		$eventNames = array(
			'Helper.Layout.afterFilter',
			'Helper.Layout.beforeFilter',
		);
		App::uses('View', 'View');
		$View = new View();
		foreach ($eventNames as $name) {
			$event = Croogo::dispatchEvent($name, $View);
			$this->assertTrue($event->result, sprintf('Event: %s', $name));
			$this->assertInstanceOf('View', $event->subject());
		}
	}

}
