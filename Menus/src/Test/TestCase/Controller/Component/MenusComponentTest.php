<?php

namespace Croogo\Menus\Test\TestCase\Controller\Component;

use Cake\Controller\Controller;
use Croogo\TestSuite\CroogoControllerTestCase;

class MenusComponentTest extends CroogoControllerTestCase
{

    public $fixtures = [
        'plugin.Blocks',
        'plugin.Blocks',
        'plugin.Menus',
        'plugin.Menus',
    ];

    public function setUp(): void
    {
        $this->_paths = App::paths();
        $app = Plugin::path('Menus') . 'Test' . DS . 'test_app' . DS;
        App::build([
            'Controller' => [
                $app . 'Controller' . DS,
            ],
            'View' => [
                $app . 'View' . DS,
            ],
        ]);
        $this->generate('MenusTest');
    }

    public function tearDown(): void
    {
        App::paths($this->_paths);
        unset($this->controller);
    }

    /**
     * test that public Links are displayed
     */
    public function testMenuGenerationForPublic(): void
    {
        $vars = $this->testAction('/index', [
            'return' => 'vars',
        ]);
        $result = Hash::extract(
            $vars['menusForLayout'],
            'footer.threaded.{n}.Link[title=Public Link Only]'
        );
        $this->assertNotEmpty($result);
    }

    /**
     * test that public Links are not displayed
     */
    public function testMenuGenerationForRegistered(): void
    {
        $this->controller->Session->write('Auth.User', ['id' => 3, 'role_id' => 2]);
        $vars = $this->testAction('/index', [
            'return' => 'vars',
        ]);
        $result = Hash::extract(
            $vars['menusForLayout'],
            'footer.threaded.{n}.Link[title=Public Link Only]'
        );
        $this->assertEmpty($result);
        $this->controller->Session->delete('Auth');
    }
}

//phpcs:disable
class MenusTestController extends Controller
{

    public $components = [
        'Auth',
        'Session',
        'Croogo.Croogo',
        'Blocks.Blocks',
        'Menus.Menus',
    ];

    public function beforeFilter(): void
    {
        $this->Auth->allow('index');
        parent::beforeFilter();
    }

    public function index(): void
    {
    }
}
//phpcs:enable
