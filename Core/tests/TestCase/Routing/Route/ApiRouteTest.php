<?php

namespace Croogo\Core\Test\TestCase\Routing\Route;

use Cake\Core\Configure;
use Croogo\Core\Routing\Route\ApiRoute;
use Croogo\Core\TestSuite\CroogoTestCase;

class ApiRouteTest extends CroogoTestCase
{

    public $fixtures = [
//		'plugin.croogo\settings.setting',
    ];

    public function testParse()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $apiPath = Configure::read('Croogo.Api.path');
        $url = '/' . $apiPath . '/v1.0/users/';

        $route = new ApiRoute('/:api/:prefix/users/:action/*', [
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'index',
        ]);

        $result = $route->parse('/' . $apiPath . '/x1.0/users/index');
        $this->assertFalse($result);

        $result = $route->parse('/foo/v1.0/users/index');
        $this->assertFalse($result);

        $result = $route->parse($url . 'index');
        $expected = [
            'api' => 'api',
            'prefix' => 'v1_0',
            'controller' => 'users',
            'action' => 'index',
            'named' => [],
            'pass' => [],
            'plugin' => 'users',
        ];
        $this->assertEquals($expected, $result);

        $result = $route->parse($url . 'lookup/pass/1/name:foo');

        $expected = [
            'api' => 'api',
            'prefix' => 'v1_0',
            'controller' => 'users',
            'action' => 'lookup',
            'named' => ['name' => 'foo'],
            'pass' => ['pass', 1],
            'plugin' => 'users',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMatch()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $apiPath = Configure::read('Croogo.Api.path');
        $url = '/' . $apiPath . '/v1.0/users/';

        $route = new ApiRoute('/:api/:prefix/users/:action/*', [
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'index',
        ]);

        $result = $route->match([
            'api' => 'api',
            'prefix' => 'v1_0',
            'controller' => 'users',
            'action' => 'index',
            'plugin' => 'users',
        ]);
        $this->assertEquals($url, $result);

        $result = $route->match([
            'api' => 'api',
            'prefix' => 'v1_0',
            'controller' => 'users',
            'action' => 'lookup',
            'plugin' => 'users',
            'name' => 'foo',
            'pass',
            1
        ]);
        $this->assertEquals($url . 'lookup/pass/1/name:foo', $result);
    }
}
