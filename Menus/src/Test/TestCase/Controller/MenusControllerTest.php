<?php
namespace Croogo\Menus\Test\TestCase\Controller;

use Croogo\TestSuite\CroogoControllerTestCase;
use Menus\Controller\MenusController;

class MenusControllerTest extends CroogoControllerTestCase
{

    public $fixtures = [
        'plugin.Users',
        'plugin.Users',
        'plugin.Users',
        'plugin.Blocks',
        'plugin.Comments',
        'plugin.Contacts',
        'plugin.Translate',
        'plugin.Settings',
        'plugin.Contacts',
        'plugin.Nodes',
        'plugin.Taxonomy',
        'plugin.Blocks',
        'plugin.Users',
        'plugin.Settings',
        'plugin.Menus',
        'plugin.Menus',
        'plugin.Meta',
        'plugin.Taxonomy',
        'plugin.Taxonomy',
        'plugin.Taxonomy',
        'plugin.Taxonomy',
        'plugin.Users',
        'plugin.Taxonomy',
    ];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->MenusController = $this->generate('Menus.Menus', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
            ],
        ]);
        $this->MenusController->Auth
            ->staticExpects($this->any())
            ->method('user')
            ->will($this->returnCallback([$this, 'authUserCallback']));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->MenusController);
    }

    /**
     * testAdminIndex
     *
     * @return void
     */
    public function testAdminIndex(): void
    {
        $this->testAction('/admin/menus/menus/index');
        $this->assertNotEmpty($this->vars['menus']);
    }

    /**
     * testAdminAdd
     *
     * @return void
     */
    public function testAdminAdd(): void
    {
        $this->expectFlashAndRedirect('The Menu has been saved');
        $mainMenu = ClassRegistry::init('Menus.Menu')->findByAlias('main');
        $this->testAction('/admin/menus/menus/add', [
            'data' => [
                'Menu' => [
                    'title' => 'New Menu',
                    'description' => 'A new menu',
                    'alias' => 'new',
                    'link_count' => 0,
                ],
            ],
        ]);
        $newMenu = $this->MenusController->Menu->findByAlias('new');
        $this->assertEqual($newMenu['Menu']['title'], 'New Menu');
    }

    /**
     * testAdminEdit
     *
     * @return void
     */
    public function testAdminEdit(): void
    {
        $this->expectFlashAndRedirect('The Menu has been saved');
        $this->testAction('/admin/menus/menus/edit/1', [
            'data' => [
                'Menu' => [
                    'id' => 3, // main
                    'title' => 'Main Menu [modified]',
                ],
            ],
        ]);
        $result = $this->MenusController->Menu->findByAlias('main');
        $this->assertEquals('Main Menu [modified]', $result['Menu']['title']);
    }

    /**
     * testAdminDelete
     *
     * @return void
     */
    public function testAdminDelete(): void
    {
        $this->expectFlashAndRedirect('Menu deleted');
        $this->testAction('/admin/menus/menus/delete/4');
        $hasAny = $this->MenusController->Menu->hasAny([
            'Menu.alias' => 'footer',
        ]);
        $this->assertFalse($hasAny);
    }
}
