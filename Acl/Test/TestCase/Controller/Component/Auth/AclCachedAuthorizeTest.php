<?php

namespace Croogo\Acl\Test\TestCase\Controller\Component\Auth;
App::uses('AclCachedAuthorize', 'Acl.Controller/Component/Auth');
App::uses('ComponentRegistry', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AclCachedAuthorizeController extends Controller {

}

class AclCachedAuthorizeTest extends CroogoTestCase {

/**
 * setUp
 */
	public function setUp() {
		$this->apiPath = Configure::read('Croogo.Api.path');
		$this->actionPath = sprintf(
			'/%s/:prefix/:plugin/:controller/:action', $this->apiPath
		);
		$request = new CakeRequest();
		$response = $this->getMock('CakeResponse');
		$Controller = new AclCachedAuthorizeController($request, $response);
		$Controller->constructClasses();
		$Controller->startupProcess();
		$this->Controller = $Controller;
		$this->Authorize = new AclCachedAuthorize($Controller->Components);
	}

	public function tearDown() {
		unset($this->Controller);
	}

/**
 * testAction
 */
	public function testAction() {
		$request = $this->_apiRequest(array(
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'index',
		));
		$result = $this->Authorize->action($request);
		$this->assertEquals('Users/Users/index', $result);

		$request = $this->_apiRequest(array(
			'api' => $this->apiPath,
			'prefix' => 'v1.0',
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'index',
		));
		$result = $this->Authorize->action($request, $this->actionPath);
		$this->assertEquals('api/v1.0/Users/Users/index', $result);
	}

/**
 * test action() with invalid request
 */
	public function testActionWithInvalidRequest() {
		$request = $this->_apiRequest(array(
			'api' => $this->apiPath,
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'index',
		));
		$result = $this->Authorize->action($request, $this->actionPath);
		$this->assertEquals('Users/Users/index', $result);

		$request = $this->_apiRequest(array(
			'prefix' => 'v1.0',
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'index',
		));
		$result = $this->Authorize->action($request, $this->actionPath);
		$this->assertEquals('Users/Users/index', $result);
	}

}
