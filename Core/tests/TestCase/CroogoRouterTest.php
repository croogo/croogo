<?php

namespace Croogo\Core\Test\TestCase;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Croogo\Core\CroogoRouter;
use Croogo\Core\TestSuite\CroogoTestCase;

class CroogoRouterTest extends CroogoTestCase
{

    public $fixtures = [
//		'plugin.croogo/settings.setting',
//		'plugin.taxonomy.vocabulary',
//		'plugin.taxonomy.type',
//		'plugin.taxonomy.types_vocabulary',
    ];

    public function setUp()
    {
        parent::setUp();
        // This test case is only valid for 2.3.x series
        $this->skipIf(version_compare(Configure::version(), '2.3.1', '<'));
    }

/**
 * testHomeRoute
 */
    public function testHomeRoute()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $promoted = [
            'plugin' => 'Croogo/nodes',
            'controller' => 'Nodes',
            'action' => 'promoted',
        ];
        $result = CroogoRouter::connect('/', $promoted);

        $this->assertEquals(1, count($result));
        $this->assertNotEmpty($result[0]);
        $this->assertInstanceOf('Route', $result[0]);
        $reversed = Router::parse('/');
        $this->assertEquals($promoted, array_intersect_key($promoted, $reversed));

        // another route
        $index = [
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'index',
        ];
        $result = CroogoRouter::connect('/nodes', $index);
        $this->assertEquals(2, count($result));
        $reversed = Router::parse('/');
        $this->assertEquals($promoted, array_intersect_key($promoted, $reversed));

        $terms = [
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'terms',
        ];
        $result = CroogoRouter::connect('/', $terms);
        $this->assertEquals(3, count($result));

        // override '/' route
//		Router::promote();
        $reversed = Router::parse('/');
        $this->assertEquals($terms, array_intersect_key($terms, $reversed));
    }

    public function testContentType()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        Router::$initialized = true;

        $params = [
            'url' => [],
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'index',
            'type' => 'blog',
        ];
        $result = Router::reverse($params);
        $this->assertEquals('/nodes/nodes/index/type:blog', $result);

        Router::$routes = [];
        CroogoRouter::contentType('blog');
        $result = Router::reverse($params);
        $this->assertEquals('/blog', $result);

        CroogoRouter::contentType('page');
        $params = [
            'url' => [],
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'index',
            'type' => 'page',
        ];
        $result = Router::reverse($params);
        $this->assertEquals('/page', $result);

        Router::$initialized = false;
    }

    public function testRoutableContentTypes()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $Type = ClassRegistry::init('Taxonomy.Type');
        $type = $Type->create([
            'title' => 'Press Release',
            'alias' => 'press-release',
            'description' => '',
        ]);
        $Type->save($type);
        Cache::clear(false, 'croogo_types');
        $type = $Type->findByAlias('press-release');
        CroogoRouter::routableContentTypes();

        $params = [
            'url' => [],
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'index',
            'type' => 'press-release',
        ];
        $result = Router::reverse($params);
        $this->assertEquals('/nodes/nodes/index/type:press-release', $result);

        $type['Type']['params'] = 'routes=1';
        $Type->save($type);
        Cache::clear(false, 'croogo_types');
        Router::$routes = [];
        CroogoRouter::routableContentTypes();

        $result = Router::reverse($params);
        $this->assertEquals('/press-release', $result);
    }

/**
 * testWhitelistedDetectorWithInvalidIp
 */
    public function testWhitelistedDetectorWithInvalidIp()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $request = $this->getMock('Request', ['clientIp']);
        $request->addDetector('whitelisted', [
            'callback' => ['CroogoRouter', 'isWhitelistedRequest'],
        ]);

        Configure::write('Site.ipWhitelist', '127.0.0.2');
        $request->expects($this->once())
            ->method('clientIp')
            ->will($this->returnValue('8.8.8.8'));
        $this->assertFalse($request->is('whitelisted'));
    }

/**
 * testWhitelistedDetectorWithValidIp
 */
    public function testWhitelistedDetectorWithValidIp()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $request = $this->getMock('Request', ['clientIp']);
        $request->addDetector('whitelisted', [
            'callback' => ['CroogoRouter', 'isWhitelistedRequest'],
        ]);

        Configure::write('Site.ipWhitelist', '127.0.0.2');
        $request->expects($this->once())
            ->method('clientIp')
            ->will($this->returnValue('127.0.0.2'));
        $this->assertTrue($request->is('whitelisted'));
    }
}
