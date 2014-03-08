<?php
/**
 * AclAutoLoginComponent test
 *
 */

App::uses('AclAutoLoginComponent', 'Acl.Controller/Component');
App::uses('Controller', 'Controller');

class TestAclAutoLoginComponent extends AclAutoLoginComponent {

	public function setupTestVars() {
		$this->_userModel = 'User';
		$this->_fields = array(
			'username' => 'username', 'password' => 'password',
		);
		$this->Auth->authenticate = array(
			'all' => array(
				'userModel' => 'Users.User',
				'fields' => array(
					'username' => 'username',
					'password' => 'password',
				),
			),
		);
	}

	public function cookie($request) {
		return $this->_cookie($request);
	}

	public function testCookie($username) {
		$request = new CakeRequest();
		$request->data = array(
			'User' => array(
				'username' => $username,
			),
		);
		return $this->_cookie($request);
	}

}

class AclAutoLoginComponentTest extends CakeTestCase {

	private $__mockEventManager;

	public function setUp() {
		$this->skipIf(!function_exists('mcrypt_decrypt'), 'mcrypt not found');

		$this->controller = $this->getMock('Controller', array());
		$collection = new ComponentCollection();
		$collection->init($this->controller);

		$this->__mockEventManager = $this->getMock('CakeEventManager');
		$this->controller->expects($this->any())
			->method('getEventManager')
			->will($this->returnValue($this->__mockEventManager));

		$this->autoLogin = new TestAclAutoLoginComponent($collection, null);
		$this->autoLogin->setupTestVars();
	}

/**
 * Test event attaching
 */
	public function testComponentShouldListenLogoutSuccessfulEvent() {
		$this->__mockEventManager->expects($this->at(0))
			->method('attach')
			->with($this->anything(), 'Controller.Users.adminLogoutSuccessful');
		$this->autoLogin->startup($this->controller);
	}

	public function testComponentShouldListenLoginSuccessfulEvent() {
		$this->__mockEventManager->expects($this->at(1))
			->method('attach')
			->with(
				$this->contains('onAdminLoginSuccessful'),
				'Controller.Users.adminLoginSuccessful'
			);
		$this->autoLogin->startup($this->controller);
	}

/**
 * Test login succesfull event
 */
	public function testLoginSuccessful() {
		$this->autoLogin->startup($this->controller);

		$cookie = $this->autoLogin->Cookie->read('User');
		$this->assertNull($cookie);

		$request = new CakeRequest();
		$request->data = array('User' => array(
			'username' => 'rchavik',
			'password' => 'rchavik',
			'remember' => true,
		));
		$this->controller->request = $request;
		$subject = $this->controller;

		$compare = $this->autoLogin->cookie($request);

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$Event = new CakeEvent('Controller.Users.adminLoginSuccessful', $subject);
		$this->autoLogin->onAdminLoginSuccessful($Event);

		$cookie = $this->autoLogin->Cookie->read('User');
		$this->assertNotNull($cookie);
		$this->assertEquals($compare, $cookie);
	}

}
