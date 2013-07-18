<?php
/**
 * TokenAuthenticateTest file
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
App::uses('TokenAuthenticate', 'Acl.Controller/Component/Auth');
App::uses('AppModel', 'Model');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');

/**
 * Test case for FormAuthentication
 *
 * @package       Cake.Test.Case.Controller.Component.Auth
 */
class TokenAuthenticateTest extends CakeTestCase {

	public $fixtures = array('plugin.acl.multi_user');

/**
 * setup
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Collection = $this->getMock('ComponentCollection');
		$this->auth = new TokenAuthenticate($this->Collection, array(
			'fields' => array(
				'username' => 'user',
				'password' => 'password',
				'token' => 'token'
			),
			'userModel' => 'MultiUser',
		));
		$password = Security::hash('password', null, true);
		$User = ClassRegistry::init('MultiUser');
		$User->updateAll(array('password' => $User->getDataSource()->value($password)));
		$this->response = $this->getMock('CakeResponse');
	}

/**
 * test authenticate token as query parameter
 *
 * @return void
 */
	public function testAuthenticateTokenParameter() {
		$this->auth->settings['_parameter'] = 'token';
		$request = new CakeRequest('posts/index?_token=54321');

		$result = $this->auth->getUser($request, $this->response);
		$this->assertFalse($result);

		$expected = array(
			'id' => '1',
			'user' => 'mariano',
			'email' => 'mariano@example.com',
			'token' => '12345',
			'created' => '2007-03-17 01:16:23',
			'updated' => '2007-03-17 01:18:31'
		);
		$request = new CakeRequest('posts/index?_token=12345');
		$result = $this->auth->getUser($request, $this->response);
		$this->assertEquals($expected, $result);

		$this->auth->settings['parameter'] = 'tokenname';
		$request = new CakeRequest('posts/index?tokenname=12345');
		$result = $this->auth->getUser($request, $this->response);
		$this->assertEquals($expected, $result);
	}

/**
 * test authenticate token as request header
 *
 * @return void
 */
	public function testAuthenticateTokenHeader() {
		$_SERVER['HTTP_X_APITOKEN'] = '54321';
		$request = new CakeRequest('posts/index', false);

		$result = $this->auth->getUser($request, $this->response);
		$this->assertFalse($result);

		$expected = array(
			'id' => '1',
			'user' => 'mariano',
			'email' => 'mariano@example.com',
			'token' => '12345',
			'created' => '2007-03-17 01:16:23',
			'updated' => '2007-03-17 01:18:31'
		);
		$_SERVER['HTTP_X_APITOKEN'] = '12345';
		$result = $this->auth->getUser($request, $this->response);
		$this->assertEquals($expected, $result);
	}

}
