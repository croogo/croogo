<?php
/**
 * AclAutoLoginComponent test
 *
 */

App::uses('AclAutoLoginComponent', 'Acl.Controller/Component');

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

	public function readCookie($key) {
		return $this->_readCookie($key);
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

	public function setUp() {
		$this->skipIf(!function_exists('mcrypt_decrypt'), 'mcrypt not found');
		$this->controller = $this->getMock('Controller', null);
		$collection = $this->controller->Components;
		$this->autoLogin = new TestAclAutoLoginComponent($collection, null);
		$this->autoLogin->setupTestVars();
		$this->autoLogin->startup($this->controller);
	}

/**
 * Test login succesfull event
 */
	public function testLoginSuccessful() {
		$cookie = $this->autoLogin->readCookie('User');
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
		Croogo::dispatchEvent('Controller.Users.adminLoginSuccessful', $subject);
		$cookie = $this->autoLogin->readCookie('User');
		$this->assertNotNull($cookie);
		$this->assertEquals($compare, $cookie);
	}

}
