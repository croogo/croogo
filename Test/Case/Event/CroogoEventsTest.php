<?php
App::uses('CroogoTestCase', 'TestSuite');
App::uses('UsersController', 'Controller');
App::uses('NodesController', 'Controller');

class TestUsersEventController extends UsersController {
}

class TestNodesEventController extends NodesController {
}

class CroogoEventsTest extends CroogoTestCase {

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

	public function testDispatchUsersEvents() {
		$eventNames = array(
			'Controller.Users.activationFailure',
			'Controller.Users.activationSuccessful',
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
		foreach ($eventNames as $name) {
			$event = Croogo::dispatchEvent($name, $this->Users);
			$this->assertTrue($event->result, sprintf('Event: %s', $name));
			$this->assertInstanceOf('UsersController', $event->subject());
		}
	}

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
