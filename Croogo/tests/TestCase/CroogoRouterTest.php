<?php

namespace Croogo\Croogo\Test\TestCase;

use Cake\Cache\Cache;
use Cake\Routing\Router;
use Croogo\Lib\CroogoRouter;
use Croogo\TestSuite\CroogoTestCase;
class CroogoRouterTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.taxonomy.vocabulary',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
	);

	public function setUp() {
		parent::setUp();
		// This test case is only valid for 2.3.x series
		$this->skipIf(version_compare(Configure::version(), '2.3.1', '<'));
	}

/**
 * testHomeRoute
 */
	public function testHomeRoute() {
		$promoted = array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'promoted',
		);
		$result = CroogoRouter::connect('/', $promoted);

		$this->assertEquals(1, count($result));
		$this->assertNotEmpty($result[0]);
		$this->assertInstanceOf('Route', $result[0]);
		$reversed = Router::parse('/');
		$this->assertEquals($promoted, array_intersect_key($promoted, $reversed));

		// another route
		$index = array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
		);
		$result = CroogoRouter::connect('/nodes', $index);
		$this->assertEquals(2, count($result));
		$reversed = Router::parse('/');
		$this->assertEquals($promoted, array_intersect_key($promoted, $reversed));

		$terms = array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'terms',
		);
		$result = CroogoRouter::connect('/', $terms);
		$this->assertEquals(3, count($result));

		// override '/' route
		Router::promote();
		$reversed = Router::parse('/');
		$this->assertEquals($terms, array_intersect_key($terms, $reversed));
	}

	public function testContentType() {
		Router::$initialized = true;

		$params = array(
			'url' => array(),
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'blog',
		);
		$result = Router::reverse($params);
		$this->assertEquals('/nodes/nodes/index/type:blog', $result);

		Router::$routes = array();
		CroogoRouter::contentType('blog');
		$result = Router::reverse($params);
		$this->assertEquals('/blog', $result);

		CroogoRouter::contentType('page');
		$params = array(
			'url' => array(),
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'page',
		);
		$result = Router::reverse($params);
		$this->assertEquals('/page', $result);

		Router::$initialized = false;
	}

	public function testRoutableContentTypes() {
		$Type = ClassRegistry::init('Taxonomy.Type');
		$type = $Type->create(array(
			'title' => 'Press Release',
			'alias' => 'press-release',
			'description' => '',
		));
		$Type->save($type);
		Cache::clear(false, 'croogo_types');
		$type = $Type->findByAlias('press-release');
		CroogoRouter::routableContentTypes();

		$params = array(
			'url' => array(),
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'press-release',
		);
		$result = Router::reverse($params);
		$this->assertEquals('/nodes/nodes/index/type:press-release', $result);

		$type['Type']['params'] = 'routes=1';
		$Type->save($type);
		Cache::clear(false, 'croogo_types');
		Router::$routes = array();
		CroogoRouter::routableContentTypes();

		$result = Router::reverse($params);
		$this->assertEquals('/press-release', $result);
	}

/**
 * testWhitelistedDetectorWithInvalidIp
 */
	public function testWhitelistedDetectorWithInvalidIp() {
		$request = $this->getMock('Request', array('clientIp'));
		$request->addDetector('whitelisted', array(
			'callback' => array('CroogoRouter', 'isWhitelistedRequest'),
		));

		Configure::write('Site.ipWhitelist', '127.0.0.2');
		$request->expects($this->once())
			->method('clientIp')
			->will($this->returnValue('8.8.8.8'));
		$this->assertFalse($request->is('whitelisted'));
	}

/**
 * testWhitelistedDetectorWithValidIp
 */
	public function testWhitelistedDetectorWithValidIp() {
		$request = $this->getMock('Request', array('clientIp'));
		$request->addDetector('whitelisted', array(
			'callback' => array('CroogoRouter', 'isWhitelistedRequest'),
		));

		Configure::write('Site.ipWhitelist', '127.0.0.2');
		$request->expects($this->once())
			->method('clientIp')
			->will($this->returnValue('127.0.0.2'));
		$this->assertTrue($request->is('whitelisted'));
	}

}
