<?php

namespace Croogo\Acl\Test\TestCase\Controller\Component\Auth;

use Acl\Controller\Component\Auth\AclCachedAuthorize;
use Cake\Controller\Controller;
use Croogo\TestSuite\CroogoTestCase;

class AclCachedAuthorizeController extends Controller
{

}

class AclCachedAuthorizeTest extends CroogoTestCase
{

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->apiPath = Configure::read('Croogo.Api.path');
        $this->actionPath = sprintf(
            '/%s/:prefix/:plugin/:controller/:action',
            $this->apiPath
        );
        $request = new Request();
        $response = $this->getMock('Response');
        $Controller = new AclCachedAuthorizeController($request, $response);
        $Controller->constructClasses();
        $Controller->startupProcess();
        $this->Controller = $Controller;
        $this->Authorize = new AclCachedAuthorize($Controller->Components);
    }

    public function tearDown(): void
    {
        unset($this->Controller);
    }

    /**
     * testAction
     */
    public function testAction(): void
    {
        $request = $this->_apiRequest([
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'index',
        ]);
        $result = $this->Authorize->action($request);
        $this->assertEquals('Users/Users/index', $result);

        $request = $this->_apiRequest([
            'api' => $this->apiPath,
            'prefix' => 'v1.0',
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'index',
        ]);
        $result = $this->Authorize->action($request, $this->actionPath);
        $this->assertEquals('api/v1.0/Users/Users/index', $result);
    }

    /**
     * test action() with invalid request
     */
    public function testActionWithInvalidRequest(): void
    {
        $request = $this->_apiRequest([
            'api' => $this->apiPath,
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'index',
        ]);
        $result = $this->Authorize->action($request, $this->actionPath);
        $this->assertEquals('Users/Users/index', $result);

        $request = $this->_apiRequest([
            'prefix' => 'v1.0',
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'index',
        ]);
        $result = $this->Authorize->action($request, $this->actionPath);
        $this->assertEquals('Users/Users/index', $result);
    }
}
