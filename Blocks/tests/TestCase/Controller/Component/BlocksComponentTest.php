<?php

namespace Croogo\Blocks\Test\TestCase\Controller\Component;

use Cake\Controller\Controller;
use Croogo\TestSuite\CroogoControllerTestCase;

class BlocksTestController extends Controller
{

    public $components = [
        'Auth',
        'Session',
        'Croogo.Croogo',
        'Blocks.Blocks',
    ];

    public function beforeFilter()
    {
        $this->Auth->allow('index');
        parent::beforeFilter();
    }

    public function index()
    {
    }
}

class BlocksComponentTest extends CroogoControllerTestCase
{

    public $fixtures = [
        'plugin.blocks.block',
        'plugin.blocks.region',
        'plugin.menus.menu',
        'plugin.menus.link',
    ];

    public function setUp()
    {
        $this->_paths = App::paths();
        $app = Plugin::path('Blocks') . 'Test' . DS . 'test_app' . DS;
        App::build([
            'Controller' => [
                $app . 'Controller' . DS,
            ],
            'View' => [
                $app . 'View' . DS,
            ],
        ]);
        $this->generate('BlocksTest');
    }

    public function tearDown()
    {
        App::paths($this->_paths);
        unset($this->controller);
    }

/**
 * test that public Blocks are displayed
 */
    public function testBlockGenerationForPublic()
    {
        $this->controller->layout = 'ajax';
        $vars = $this->testAction('/index', [
            'return' => 'vars',
        ]);

        $result = Hash::extract(
            $vars['blocks_for_layout'],
            'right.{n}.Block[title=Block Visible by Admin or Registered]'
        );
        $this->assertEmpty($result);

        $result = Hash::extract(
            $vars['blocks_for_layout'],
            'right.{n}.Block[title=Block Visible by Public]'
        );
        $this->assertNotEmpty($result);
    }

/**
 * test that block are displayed for Registered
 */
    public function testBlockGenerationForRegistered()
    {
        $this->controller->layout = 'ajax';
        $this->controller->Session->write('Auth.User', ['id' => 3, 'role_id' => 2]);
        $vars = $this->testAction('/index', [
            'return' => 'vars',
        ]);

        $result = Hash::extract(
            $vars['blocks_for_layout'],
            'right.{n}.Block[title=Block Visible by Public]'
        );
        $this->assertEmpty($result);

        $result = Hash::extract(
            $vars['blocks_for_layout'],
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
        $this->controller->layout = 'ajax';
        $this->controller->Session->write('Auth.User', ['id' => 1, 'role_id' => 1]);
        $vars = $this->testAction('/index', [
            'return' => 'vars',
        ]);

        $result = Hash::extract(
            $vars['blocks_for_layout'],
            'right.{n}.Block[title=Block Visible by Public]'
        );
        $this->assertEmpty($result);

        $result = Hash::extract(
            $vars['blocks_for_layout'],
            'right.{n}.Block[title=Block Visible by Admin or Registered]'
        );
        $this->assertNotEmpty($result);
        $this->controller->Session->delete('Auth');
    }
}
