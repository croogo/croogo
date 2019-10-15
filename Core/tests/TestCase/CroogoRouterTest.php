<?php

namespace Croogo\Core\Test\TestCase;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Croogo\Core\Router;
use Croogo\Core\TestSuite\TestCase;

class CroogoRouterTest extends TestCase
{

    public $fixtures = [
//      'plugin.Croogo/Settings.Setting',
//      'plugin.Croogo/Taxonomy.Vocabulary',
        'plugin.Croogo/Taxonomy.Type',
//      'plugin.Croogo/Taxonomy.TypesVocabulary',
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
        $promoted = [
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'promoted',
        ];
        Router::connect('/', $promoted);
        $result = Router::routes();

        $this->assertEquals(1, count($result));
        $this->assertNotEmpty($result[0]);
        $this->assertInstanceOf('Cake\\Routing\\Route\\Route', $result[0]);
        $homeRequest = new ServerRequest('/');
        $reversed = Router::parseRequest($homeRequest);
        $this->assertEquals($promoted, array_intersect_key($promoted, $reversed));

        // another route
        $index = [
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'index',
        ];
        Router::connect('/nodes', $index);
        $result = Router::routes();
        $this->assertEquals(2, count($result));
        $reversed = Router::parseRequest($homeRequest);
        $this->assertEquals($promoted, array_intersect_key($promoted, $reversed));

        $terms = [
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'terms',
        ];
        Router::connect('/', $terms);
        $result = Router::routes();
        $this->assertEquals(3, count($result));

        // override '/' route
//      Router::promote();
        $reversed = Router::parseRequest($homeRequest);
        $this->assertEquals($terms, array_intersect_key($terms, $reversed));
    }

    public function testContentType()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');
        // Reload plugin routes
        Router::reload();

        $params = [
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'index',
            'type' => 'blog',
        ];
        $result = Router::reverse($params);
        $this->assertEquals('/blog', $result);

        Router::reload();
        Router::contentType('blog');
        $result = Router::reverse($params);
        $this->assertEquals('/blog', $result);

        Router::contentType('page');
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
        // Reload plugin routes
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');
        Plugin::routes();

        $table = TableRegistry::get('Croogo/Taxonomy.Types');
        $type = $table->save($table->newEntity([
            'title' => 'Press Release',
            'alias' => 'press-release',
            'description' => '',
        ]));
        $table->save($type);
        Cache::clear(false, 'croogo_types');
        $type = $table->findByAlias('press-release')->first();
        Router::routableContentTypes();

        $params = [
            'url' => [],
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'index',
            'type' => 'press-release',
        ];
        $result = Router::reverse($params);
        $this->assertEquals('/press-release', $result);

        $type->params = [
            'routes' => true
        ];
        $table->save($type);
        Cache::clear(false, 'croogo_types');
        Router::reload();
        Router::routableContentTypes();

        $result = Router::reverse($params);
        $this->assertEquals('/press-release', $result);
    }

    /**
     * testWhitelistedDetectorWithInvalidIp
     */
    public function testWhitelistedDetectorWithInvalidIp()
    {
        $request = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['clientIp'])
            ->getMock();
        $request->addDetector('whitelisted', ['Croogo\\Core\\Router', 'isWhitelistedRequest']);

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
        $request = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['clientIp'])
            ->getMock();
        $request->addDetector('whitelisted', ['Croogo\\Core\\Router', 'isWhitelistedRequest']);

        Configure::write('Site.ipWhitelist', '127.0.0.2');
        $request->expects($this->once())
            ->method('clientIp')
            ->will($this->returnValue('127.0.0.2'));
        $this->assertTrue($request->is('whitelisted'));
    }
}
