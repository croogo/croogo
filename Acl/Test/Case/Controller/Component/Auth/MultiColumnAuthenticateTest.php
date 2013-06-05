<?php
/**
 * MultiColumnAuthenticateTest file
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

App::uses('AuthComponent', 'Controller/Component');
App::uses('MultiColumnAuthenticate', 'Acl.Controller/Component/Auth');
App::uses('AppModel', 'Model');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

/**
 * Test case for FormAuthentication
 *
 * @package       Cake.Test.Case.Controller.Component.Auth
 */
class MultiColumnAuthenticateTest extends CroogoTestCase {

	public $fixtures = array('plugin.acl.multi_user');

/**
 * setup
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Collection = $this->getMock('ComponentCollection');
		$this->auth = new MultiColumnAuthenticate($this->Collection, array(
			'fields' => array('username' => 'user', 'password' => 'password'),
			'userModel' => 'MultiUser',
			'columns' => array('user', 'email')
		));
		$password = Security::hash('password', null, true);
		$User = ClassRegistry::init('MultiUser');
		$User->updateAll(array('password' => $User->getDataSource()->value($password)));
		$this->response = $this->getMock('CakeResponse');
	}

/**
 * test authenticate email or username
 *
 * @return void
 */
	public function testAuthenticateEmailOrUsername() {
		$request = new CakeRequest('posts/index', false);
		$expected = array(
			'id' => 1,
			'user' => 'mariano',
			'email' => 'mariano@example.com',
			'created' => '2007-03-17 01:16:23',
			'updated' => '2007-03-17 01:18:31'
		);

		$request->data = array('MultiUser' => array(
			'user' => 'mariano',
			'password' => 'password'
		));
		$result = $this->auth->authenticate($request, $this->response);
		$this->assertEquals($expected, $result);

		$request->data = array('MultiUser' => array(
			'user' => 'mariano@example.com',
			'password' => 'password'
		));
		$result = $this->auth->authenticate($request, $this->response);
		$this->assertEquals($expected, $result);
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoData() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array();
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoUsername() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array('MultiUser' => array('password' => 'foobar'));
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateNoPassword() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array('MultiUser' => array('user' => 'mariano'));
		$this->assertFalse($this->auth->authenticate($request, $this->response));

		$request->data = array('MultiUser' => array('user' => 'mariano@example.com'));
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test the authenticate method
 *
 * @return void
 */
	public function testAuthenticateInjection() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array(
			'MultiUser' => array(
				'user' => '> 1',
				'password' => "' OR 1 = 1"
		));
		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

/**
 * test scope failure.
 *
 * @return void
 */
	public function testAuthenticateScopeFail() {
		$this->auth->settings['scope'] = array('user' => 'nate');
		$request = new CakeRequest('posts/index', false);
		$request->data = array('User' => array(
			'user' => 'mariano',
			'password' => 'password'
		));

		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

}
