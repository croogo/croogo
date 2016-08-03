<?php

namespace Croogo\Blocks\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Croogo\Blocks\Controller\Component\BlocksComponent;
use Croogo\Core\TestSuite\IntegrationTestCase;
use Croogo\Core\TestSuite\TestCase;
use Croogo\TestSuite\CroogoControllerTestCase;

class BlocksComponentTest extends IntegrationTestCase
{
    public $fixtures = [
        'plugin.croogo/blocks.block',
        'plugin.croogo/blocks.region',
        'plugin.croogo/menus.menu',
        'plugin.croogo/menus.link',
        'plugin.croogo/taxonomy.type',
        'plugin.croogo/taxonomy.vocabulary',
        'plugin.croogo/taxonomy.taxonomy',
        'plugin.croogo/taxonomy.term',
        'plugin.croogo/taxonomy.model_taxonomy',
        'plugin.croogo/comments.comment',
        'plugin.croogo/meta.meta',
        'plugin.croogo/nodes.node',
        'plugin.croogo/users.role',
        'plugin.croogo/users.user',
        'plugin.croogo/users.aro',
        'plugin.croogo/users.aco',
        'plugin.croogo/users.aros_aco',
    ];

    /**
     * @var \Cake\Controller\Controller
     */
    public $controller;

    /**
     * @var \Croogo\Blocks\Controller\Component\BlocksComponent
     */
    public $component;

    public function setUp()
    {
        parent::setUp();

//        $request = new Request;
//        $response = new Response;
//        $this->controller = $this->getMock(
//            'Cake\Controller\Controller',
//            null,
//            [$request, $response]
//        );
//        $this->controller->loadComponent('Croogo/Core.Croogo');
//        $this->component = $this->controller->components()->load('Croogo/Blocks.Blocks');
//
//        $this->_paths = App::paths();
//        $app = Plugin::path('Blocks') . 'Test' . DS . 'test_app' . DS;
//        App::build([
//            'Controller' => [
//                $app . 'Controller' . DS,
//            ],
//            'View' => [
//                $app . 'View' . DS,
//            ],
//        ]);
//        $this->generate('BlocksTest');
    }

//    public function tearDown()
//    {
//        App::paths($this->_paths);
//        unset($this->controller);
//    }

/**
 * test that public Blocks are displayed
 */
    public function testBlockGenerationForPublic()
    {
        $this->markTestIncomplete('This test is being ported');

//        $this->get('/');

//        debug($this->_response);

        return;

//        $this->controller->startupProcess();
////        $this->controller->viewBuilder()->layout('ajax');
//
//        debug($this->component->blocks());
//
//        $this->controller->render();
//
//        debug($this->controller->viewBuilder());
//
//        return;

        $vars = $this->testAction('/index', [
            'return' => 'vars',
        ]);

        $result = Hash::extract(
            $vars['blocksForLayout'],
            'right.{n}.Block[title=Block Visible by Admin or Registered]'
        );
        $this->assertEmpty($result);

        $result = Hash::extract(
            $vars['blocksForLayout'],
            'right.{n}.Block[title=Block Visible by Public]'
        );
        $this->assertNotEmpty($result);
    }

/**
 * test that block are displayed for Registered
 */
    public function testBlockGenerationForRegistered()
    {
        $this->markTestIncomplete('This test is being ported');
        $this->user('registered-user');

        $this->get('/');

//        debug($this->_response->body());
//        debug($this->_controller);
        $this->assertEmpty(collection($this->viewVariable('blocksForLayout')['right'])->match([
            'title' => 'Block Visible by Public'
        ])->toArray());

        $this->assertNotEmpty(collection($this->viewVariable('blocksForLayout')['right'])->match([
            'title' => 'Block Visible by Admin or Registered'
        ])->toArray());

        exit();

        $this->controller->layout = 'ajax';
        $this->controller->Session->write('Auth.User', ['id' => 3, 'role_id' => 2]);
        $vars = $this->testAction('/index', [
            'return' => 'vars',
        ]);

        $result = Hash::extract(
            $vars['blocksForLayout'],
            'right.{n}.Block[title=Block Visible by Public]'
        );
        $this->assertEmpty($result);

        $result = Hash::extract(
            $vars['blocksForLayout'],
            'right.{n}.Block[title=Block Visible by Admin or Registered]'
        );
        $this->assertNotEmpty($result);
        $this->controller->Session->delete('Auth');
    }

/**
 * test that block are displayed for Admin
 */
    public function testBlockGenerationForAdmin()
    {
        $this->markTestIncomplete('This test is being ported');

        $this->controller->layout = 'ajax';
        $this->controller->Session->write('Auth.User', ['id' => 1, 'role_id' => 1]);
        $vars = $this->testAction('/index', [
            'return' => 'vars',
        ]);

        $result = Hash::extract(
            $vars['blocksForLayout'],
            'right.{n}.Block[title=Block Visible by Public]'
        );
        $this->assertEmpty($result);

        $result = Hash::extract(
            $vars['blocksForLayout'],
            'right.{n}.Block[title=Block Visible by Admin or Registered]'
        );
        $this->assertNotEmpty($result);
        $this->controller->Session->delete('Auth');
    }
}
