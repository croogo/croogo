<?php
/**
 * CookieAuthenticateTest file
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.Case.Controller.Component.Auth
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Croogo\Acl\Test\TestCase\Controller\Component\Auth;

require_once dirname(__FILE__) . '/../AclAutoLoginComponentTest.php';
App::uses('CookieAuthenticate', 'Acl.Controller/Component/Auth');

class TestCookieAuthenticate extends CookieAuthenticate {

	public function verify($cookie) {
		return $this->_verify($cookie);
	}

}

/**
 * Test case for CookieAuthenticate
 *
 */
class CookieAuthenticateTest extends CakeTestCase {

/**
 * setup
 */
	public function setUp() {
		$this->skipIf(!function_exists('mcrypt_decrypt'), 'mcrypt not found');
		$this->controller = $this->getMock('Controller', null);
		$collection = $this->controller->Components;
		$this->autoLogin = new TestAclAutoLoginComponent($collection, null);
		$this->cookieAuth = new TestCookieAuthenticate($collection, null);
		$this->autoLogin->setupTestVars();
		$this->autoLogin->startup($this->controller);
	}

/**
 * Test verify
 */
	public function testVerifySuccessful() {
		$username = 'teresa';
		$cookie = $this->autoLogin->testCookie($username);

		$this->assertTrue(isset($cookie['data']));
		$this->assertTrue(isset($cookie['mac']));

		$result = $this->cookieAuth->verify($cookie);
		$this->assertEquals($username, $result['username']);
	}

/**
 * Verify against tampered data
 */
	public function testVerifyTamperedCookie() {
		$username = 'rchavik';
		$cookie = $this->autoLogin->testCookie('rchavik');

		$tampered = $cookie;
		$tampered['data'] = str_replace('rchavik', 'yvonne', $cookie['data']);
		$result = $this->cookieAuth->verify($tampered);
		$this->assertFalse($result);

		$data = json_decode($cookie['data'], true);
		unset($data['hash']);
		$tampered = $cookie;
		$tampered['data'] = json_encode($data);
		$result = $this->cookieAuth->verify($tampered);
		$this->assertFalse($result);
	}

/**
 * Test Ignore requests with data
 */
	public function testIgnoreRequestWithData() {
		$request = $this->getMock('CakeRequest', null);
		$response = $this->getMock('CakeResponse');
		$request->data = array('User' => array('somedata'));
		$collection = $this->controller->Components;
		$cookieAuth = $this->getMock(
			'TestCookieAuthenticate', array('getUser'), array($collection, null)
		);
		$cookieAuth->expects($this->never())->method('getUser');
		$result = $cookieAuth->authenticate($request, $response);
		$this->assertFalse($result);
	}

/**
 * Test Ignore POST requests
 */
	public function testIgnorePostRequest() {
		$request = $this->getMock('CakeRequest', null);
		$response = $this->getMock('CakeResponse');
		$collection = $this->controller->Components;
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$cookieAuth = $this->getMock(
			'TestCookieAuthenticate', array('getUser'), array($collection, null)
		);
		$cookieAuth->expects($this->never())->method('getUser');
		$result = $cookieAuth->authenticate($request, $response);
		$this->assertFalse($result);
	}

}
