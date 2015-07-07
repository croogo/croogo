<?php

namespace Croogo\Core\Test\TestCase\Routing\Route;

use Cake\Core\Configure;
use Croogo\Core\Routing\Route\ApiRoute;
use Croogo\Core\TestSuite\CroogoTestCase;
class ApiRouteTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.croogo\settings.setting',
	);

	public function testParse() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$apiPath = Configure::read('Croogo.Api.path');
		$url = '/' . $apiPath . '/v1.0/users/';

		$route = new ApiRoute('/:api/:prefix/users/:action/*', array(
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'index',
		));

		$result = $route->parse('/' . $apiPath . '/x1.0/users/index');
		$this->assertFalse($result);

		$result = $route->parse('/foo/v1.0/users/index');
		$this->assertFalse($result);

		$result = $route->parse($url . 'index');
		$expected = array(
			'api' => 'api',
			'prefix' => 'v1_0',
			'controller' => 'users',
			'action' => 'index',
			'named' => array(),
			'pass' => array(),
			'plugin' => 'users',
		);
		$this->assertEquals($expected, $result);

		$result = $route->parse($url . 'lookup/pass/1/name:foo');

		$expected = array(
			'api' => 'api',
			'prefix' => 'v1_0',
			'controller' => 'users',
			'action' => 'lookup',
			'named' => array('name' => 'foo'),
			'pass' => array('pass', 1),
			'plugin' => 'users',
		);

		$this->assertEquals($expected, $result);
	}

	public function testMatch() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$apiPath = Configure::read('Croogo.Api.path');
		$url = '/' . $apiPath . '/v1.0/users/';

		$route = new ApiRoute('/:api/:prefix/users/:action/*', array(
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'index',
		));

		$result = $route->match(array(
			'api' => 'api',
			'prefix' => 'v1_0',
			'controller' => 'users',
			'action' => 'index',
			'plugin' => 'users',
		));
		$this->assertEquals($url, $result);

		$result = $route->match(array(
			'api' => 'api',
			'prefix' => 'v1_0',
			'controller' => 'users',
			'action' => 'lookup',
			'plugin' => 'users',
			'name' => 'foo',
			'pass',
			1
		));
		$this->assertEquals($url . 'lookup/pass/1/name:foo', $result);
	}

}
