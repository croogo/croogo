<?php

namespace Croogo\Menus\Test\TestCase\View\Helper;

use App\Controller\Component\SessionComponent;
use Cake\Controller\Controller;
use Croogo\Core\TestSuite\TestCase;
use Menus\View\Helper\MenusHelper;

class MenusHelperTest extends TestCase
{

    public array $fixtures = [
        'plugin.Users',
        'plugin.Users',
        'plugin.Settings',
    ];

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ComponentRegistry = new ComponentRegistry();

        $request = $this->getMock('Request');
        $response = $this->getMock('Response');
        $this->View = new View(new TheMenuTestController($request, $response));
        $this->Menus = new MenusHelper($this->View);
        $this->_appEncoding = Configure::read('App.encoding');
        $this->_asset = Configure::read('Asset');
        $this->_debug = Configure::read('debug');
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        Configure::write('App.encoding', $this->_appEncoding);
        Configure::write('Asset', $this->_asset);
        Configure::write('debug', $this->_debug);
        ClassRegistry::flush();
        unset($this->Layout);
    }

    /**
     * Test [menu] shortcode
     */
    public function testMenuShortcode(): void
    {
        $content = '[menu:blogroll]';
        $this->View->viewVars['menusForLayout']['blogroll'] = [
            'Menu' => [
                'id' => 6,
                'title' => 'Blogroll',
                'alias' => 'blogroll',
            ],
            'threaded' => [],
        ];
        Croogo::dispatchEvent('Helper.Layout.beforeFilter', $this->View, ['content' => &$content]);
        $this->assertContains('menu-6', $content);
        $this->assertContains('class="menu"', $content);
    }
}

//phpcs:disable
class TheMenuTestController extends Controller
{

    public string $name = 'TheTest';

    public $uses = null;
}
//phpcs:enable
