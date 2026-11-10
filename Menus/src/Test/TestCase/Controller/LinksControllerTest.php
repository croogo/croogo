<?php
namespace Croogo\Menus\Test\TestCase\Controller;

use Croogo\TestSuite\CroogoControllerTestCase;
use Menus\Controller\LinksController;

class LinksControllerTest extends CroogoControllerTestCase
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
        'plugin.Meta',
        'plugin.Nodes',
        'plugin.Taxonomy',
        'plugin.Blocks',
        'plugin.Users',
        'plugin.Settings',
        'plugin.Menus',
        'plugin.Menus',
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
        $this->LinksController = $this->generate('Menus.Links', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
            ],
        ]);
        $this->LinksController->Auth
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
        unset($this->LinksController);
    }

    /**
     * checks that we were redirected with menu id
     */
    protected function _expectsRedirectToMenu($menuId)
    {
        $this->controller->expects($this->once())
            ->method('redirect')
            ->with([
                'action' => 'index',
                '?' => ['menu_id' => $menuId],
            ]);
    }

    /**
     * testAdminIndex
     *
     * @return void
     */
    public function testAdminIndex(): void
    {
        $this->LinksController
            ->expects($this->once())
            ->method('redirect')
            ->with(
                $this->equalTo([
                    'controller' => 'Menus',
                    'action' => 'index',
                ])
            );
        $this->testAction('/admin/menus/links/index');
        $this->testAction('/admin/menus/links/index/?menu_id=3');
        $mainMenu = $this->LinksController->Link->Menu->findByAlias('main');
        $this->assertEquals($mainMenu, $this->vars['menu']);
        $this->assertNotEmpty($this->vars['linksTree']);
        $this->assertNotEmpty($this->vars['linksStatus']);
    }

    /**
     * testAdminAdd
     *
     * @return void
     */
    public function testAdminAdd(): void
    {
        $this->expectFlashAndRedirect('The Link has been saved');
        $mainMenu = ClassRegistry::init('Menus.Menu')->findByAlias('main');
        $this->_expectsRedirectToMenu($mainMenu['Menu']['id']);
        $this->testAction('/admin/menus/links/add', [
            'data' => [
                'Link' => [
                    'menu_id' => $mainMenu['Menu']['id'],
                    'title' => 'Test link',
                    'class' => 'test-link',
                    'link' => '#test-link',
                    'status' => 1,
                ],
                'Role' => [
                    'Role' => [],
                ],
            ],
        ]);
        $testLink = $this->LinksController->Link->findByLink('#test-link');
        $this->assertEqual($testLink['Link']['title'], 'Test link');
    }

    /**
     * testAdminEdit
     *
     * @return void
     */
    public function testAdminEdit(): void
    {
        $this->expectFlashAndRedirect('The Link has been saved');
        $homeLink = $this->LinksController->Link->find('first', [
            'conditions' => [
                'Link.title' => 'Home',
                'Link.link' => '/',
            ],
        ]);
        $this->_expectsRedirectToMenu($homeLink['Link']['menu_id']);
        $this->testAction('/admin/links/edit/' . $homeLink['Link']['id'], [
            'data' => [
                'Link' => [
                    'id' => $homeLink['Link']['id'],
                    'menu_id' => $homeLink['Link']['menu_id'],
                    'title' => 'Home [modified]',
                    'link' => '/',
                    'status' => 1,
                ],
                'Role' => [
                    'Role' => [],
                ],
            ],
        ]);
        $result = $this->LinksController->Link->findById($homeLink['Link']['id']);
        $this->assertEquals('Home [modified]', $result['Link']['title']);
    }

    public function testAdminEditShouldReorderLinkWhenChangingLinkMenu(): void
    {
        $contactLink = $this->LinksController->Link->findById(15);

        $this->testAction('/admin/links/edit/' . $contactLink['Link']['id'], [
            'data' => [
                'Link' => array_merge($contactLink['Link'], ['menu_id' => 5]),
                'Role' => ['Role' => []],
            ],
        ]);

        $newContactLink = $this->LinksController->Link->findById($contactLink['Link']['id']);
        $this->assertNotEquals($contactLink['Link']['lft'], $newContactLink['Link']['lft']);
    }

    public function testAdminEditShouldReorderOldMenuWhenChangingLinkMenu(): void
    {
        $homeLink = $this->LinksController->Link->findById(7);

        $this->testAction('/admin/links/edit/' . $homeLink['Link']['id'], [
            'data' => [
                'Link' => array_merge($homeLink['Link'], ['menu_id' => 5]),
                'Role' => ['Role' => []],
            ],
        ]);

        $newAboutLink = $this->LinksController->Link->findById(8);
        $this->assertEquals(1, $newAboutLink['Link']['lft']);
    }

    /**
     * testAdminDelete
     *
     * @return void
     */
    public function testAdminDelete(): void
    {
        $this->expectFlashAndRedirect('Link deleted');
        $homeLink = ClassRegistry::init('Menus.Link')->find('first', [
            'conditions' => [
                'Link.title' => 'Home',
                'Link.link' => '/',
            ],
        ]);
        $this->_expectsRedirectToMenu($homeLink['Link']['menu_id']);
        $this->testAction('/admin/menus/links/delete/' . $homeLink['Link']['id']);
        $hasAny = $this->LinksController->Link->hasAny([
            'Link.title' => 'Home',
            'Link.link' => '/',
        ]);
        $this->assertFalse($hasAny);
    }

    /**
     * testAdminMoveUp
     *
     * @return void
     */
    public function testAdminMoveUp(): void
    {
        $this->expectFlashAndRedirect('Moved up successfully');
        $mainMenu = ClassRegistry::init('Menus.Menu')->findByAlias('main');
        $aboutLink = ClassRegistry::init('Menus.Link')->find('first', [
            'conditions' => [
                'Link.menu_id' => $mainMenu['Menu']['id'],
                'Link.title' => 'About',
                'Link.link' => '/about',
            ],
        ]);
        $this->_expectsRedirectToMenu($aboutLink['Link']['menu_id']);
        $this->testAction('/admin/menus/links/moveup/' . $aboutLink['Link']['id']);
        $list = $this->LinksController->Link->generateTreeList([
            'Link.menu_id' => $mainMenu['Menu']['id'],
            'Link.status' => 1,
        ]);
        $linkTitles = array_values($list);
        $this->assertEqual($linkTitles, [
            'About',
            'Home',
            'Contact'
        ]);
    }

    /**
     * testAdminMoveUpWithSteps
     *
     * @return void
     */
    public function testAdminMoveUpWithSteps(): void
    {
        $this->expectFlashAndRedirect('Moved up successfully');
        $mainMenu = ClassRegistry::init('Menus.Menu')->findByAlias('main');
        $contactLink = ClassRegistry::init('Menus.Link')->find('first', [
            'conditions' => [
                'Link.menu_id' => $mainMenu['Menu']['id'],
                'Link.title' => 'Contact',
            ],
        ]);
        $this->_expectsRedirectToMenu($contactLink['Link']['menu_id']);
        $this->testAction('/admin/menus/links/moveup/' . $contactLink['Link']['id'] . '/' . 2);
        $list = $this->LinksController->Link->generateTreeList([
            'Link.menu_id' => $mainMenu['Menu']['id'],
            'Link.status' => 1,
        ]);
        $linkTitles = array_values($list);
        $this->assertEqual($linkTitles, [
            'Contact',
            'Home',
            'About',
        ]);
    }

    /**
     * testAdminMoveDown
     *
     * @return void
     */
    public function testAdminMoveDown(): void
    {
        $this->expectFlashAndRedirect('Moved down successfully');
        $mainMenu = ClassRegistry::init('Menus.Menu')->findByAlias('main');
        $aboutLink = ClassRegistry::init('Menus.Link')->find('first', [
            'conditions' => [
                'Link.menu_id' => $mainMenu['Menu']['id'],
                'Link.title' => 'About',
                'Link.link' => '/about',
            ],
        ]);
        $this->_expectsRedirectToMenu($aboutLink['Link']['menu_id']);
        $this->testAction('/admin/menus/links/movedown/' . $aboutLink['Link']['id']);
        $list = $this->LinksController->Link->generateTreeList([
            'Link.menu_id' => $mainMenu['Menu']['id'],
            'Link.status' => 1,
        ]);
        $linkTitles = array_values($list);
        $this->assertEqual($linkTitles, [
            'Home',
            'Contact',
            'About',
        ]);
    }

    /**
     * testAdminMoveDownWithSteps
     *
     * @return void
     */
    public function testAdminMoveDownWithSteps(): void
    {
        $this->expectFlashAndRedirect('Moved down successfully');
        $mainMenu = ClassRegistry::init('Menus.Menu')->findByAlias('main');
        $homeLink = ClassRegistry::init('Menus.Link')->find('first', [
            'conditions' => [
                'Link.menu_id' => $mainMenu['Menu']['id'],
                'Link.title' => 'Home',
            ],
        ]);
        $this->_expectsRedirectToMenu($homeLink['Link']['menu_id']);
        $this->testAction('/admin/menus/links/movedown/' . $homeLink['Link']['id'] . '/' . 2);
        $list = $this->LinksController->Link->generateTreeList([
            'Link.menu_id' => $mainMenu['Menu']['id'],
            'Link.status' => 1,
        ]);
        $linkTitles = array_values($list);
        $this->assertEqual($linkTitles, [
            'About',
            'Contact',
            'Home',
        ]);
    }
}
