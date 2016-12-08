<?php
namespace Croogo\Core\Test\TestCase;

use Cake\Utility\Hash;
use Croogo\Core\Croogo;
use Croogo\Core\Nav;
use Croogo\Core\TestSuite\CroogoTestCase;

class CroogoNavTest extends CroogoTestCase
{

    public $setupSettings = false;

    protected static $_menus = [];

    public function setUp()
    {
        parent::setUp();
        self::$_menus = Nav::items('sidebar');
        Nav::activeMenu('sidebar');
    }

    public function tearDown()
    {
        parent::tearDown();
        Nav::clear(null);
        Nav::items('sidebar', self::$_menus);
    }

    public function testNav()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $saved = Nav::items();

        // test clear
        Nav::clear();
        $items = Nav::items();
        $this->assertEquals($items, []);

        // test first level addition
        $defaults = Nav::getDefaults();
        $extensions = ['title' => 'Extensions'];
        Nav::add('extensions', $extensions);
        $result = Nav::items();
        $expected = ['extensions' => Hash::merge($defaults, $extensions)];
        $this->assertEquals($result, $expected);

        // tested nested insertion (1 level)
        $plugins = ['title' => 'Plugins'];
        Nav::add('extensions.children.plugins', $plugins);
        $result = Nav::items();
        $expected['extensions']['children']['plugins'] = Hash::merge($defaults, $plugins);
        $this->assertEquals($result, $expected);

        // 2 levels deep
        $example = ['title' => 'Example'];
        Nav::add('extensions.children.plugins.children.example', $example);
        $result = Nav::items();

        $expected['extensions']['children']['plugins']['children']['example'] = Hash::merge($defaults, $example);
        $this->assertEquals($result, $expected);

        Nav::items('sidebar', $saved);
        $this->assertEquals($saved, Nav::items());
    }

/**
 * @expectedException UnexpectedValueException
 */
    public function testNavClearWithException()
    {
        Nav::clear('bogus');
    }

/**
 * testNavItemsWithBogusMenu
 */
    public function testNavItemsWithBogusMenu()
    {
        $result = Nav::items('bogus');
        $this->assertEquals([], $result);
    }

/**
 * Test Get Menus
 */
    public function testNavGetMenus()
    {
        $result = Nav::menus();
        $this->assertEquals(['sidebar'], $result);

        Nav::activeMenu('top');
        Nav::add('foo', ['title' => 'foo']);

        $result = Nav::menus();
        $this->assertEquals(['sidebar', 'top'], $result);
    }

/**
 * Test multiple menu
 */
    public function testNavMultipleMenus()
    {
        Nav::activeMenu('top');
        Nav::add('foo', ['title' => 'foo']);

        $menus = array_keys(Nav::items());
        $this->assertFalse(in_array('foo', $menus), 'foo exists in sidebar');

        $menus = array_keys(Nav::items('top'));
        $this->assertTrue(in_array('foo', $menus), 'foo missing in top');
    }

    public function testNavMerge()
    {
        $foo = ['title' => 'foo', 'access' => ['public', 'admin']];
        $bar = ['title' => 'bar', 'access' => ['admin']];
        Nav::clear();
        Nav::add('foo', $foo);
        Nav::add('foo', $bar);
        $items = Nav::items();
        $expected = ['admin', 'public'];
        sort($expected);
        sort($items['foo']['access']);
        $this->assertEquals($expected, $items['foo']['access']);
    }

    public function testNavMergeSameArray()
    {
        Nav::clear();
        $foo = array('url' => array('action' => 'index', 'hi'));
        Nav::add('foo', $foo);
        Nav::add('foo', $foo);
        $items = Nav::items();
        $expected = array('action' => 'index', 'hi');
        $this->assertEquals($expected, $items['foo']['url']);
    }

    public function testNavOverwrite()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        Croogo::dispatchEvent('Croogo.setupAdminData', null);
        $defaults = Nav::getDefaults();

        $items = Nav::items();
        $expected = Hash::merge($defaults, [
            'title' => 'Permissions',
            'url' => [
                'admin' => true,
                'plugin' => 'acl',
                'controller' => 'acl_permissions',
                'action' => 'index',
            ],
            'weight' => 30,
        ]);
        $this->assertEquals($expected, $items['users']['children']['permissions']);

        $item = [
            'title' => 'Permissions',
            'url' => [
                'admin' => true,
                'plugin' => 'acl_extras',
                'controller' => 'acl_extras_permissions',
                'action' => 'index',
            ],
            'weight' => 30,
        ];
        Nav::add('users.children.permissions', $item);
        $items = Nav::items();

        $expected = Hash::merge($defaults, [
            'title' => 'Permissions',
            'url' => [
                'admin' => true,
                'plugin' => 'acl_extras',
                'controller' => 'acl_extras_permissions',
                'action' => 'index',
            ],
            'weight' => 30,
        ]);

        $this->assertEquals($expected, $items['users']['children']['permissions']);
    }
}
