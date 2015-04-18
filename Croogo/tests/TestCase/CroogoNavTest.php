<?php
namespace Croogo\Croogo\Test\TestCase;

use Croogo\Lib\CroogoNav;
use Croogo\TestSuite\CroogoTestCase;
class CroogoNavTest extends CroogoTestCase {

	public $setupSettings = false;

	protected static $_menus = array();

	public function setUp() {
		parent::setUp();
		self::$_menus = CroogoNav::items('sidebar');
		CroogoNav::activeMenu('sidebar');
	}

	public function tearDown() {
		parent::tearDown();
		CroogoNav::clear(null);
		CroogoNav::items('sidebar', self::$_menus);
	}

	public function testNav() {
		$saved = CroogoNav::items();

		// test clear
		CroogoNav::clear();
		$items = CroogoNav::items();
		$this->assertEqual($items, array());

		// test first level addition
		$defaults = CroogoNav::getDefaults();
		$extensions = array('title' => 'Extensions');
		CroogoNav::add('extensions', $extensions);
		$result = CroogoNav::items();
		$expected = array('extensions' => Hash::merge($defaults, $extensions));
		$this->assertEqual($result, $expected);

		// tested nested insertion (1 level)
		$plugins = array('title' => 'Plugins');
		CroogoNav::add('extensions.children.plugins', $plugins);
		$result = CroogoNav::items();
		$expected['extensions']['children']['plugins'] = Hash::merge($defaults, $plugins);
		$this->assertEqual($result, $expected);

		// 2 levels deep
		$example = array('title' => 'Example');
		CroogoNav::add('extensions.children.plugins.children.example', $example);
		$result = CroogoNav::items();

		$expected['extensions']['children']['plugins']['children']['example'] = Hash::merge($defaults, $example);
		$this->assertEqual($result, $expected);

		CroogoNav::items('sidebar', $saved);
		$this->assertEquals($saved, CroogoNav::items());
	}

/**
 * @expectedException UnexpectedValueException
 */
	public function testNavClearWithException() {
		CroogoNav::clear('bogus');
	}

/**
 * testNavItemsWithBogusMenu
 */
	public function testNavItemsWithBogusMenu() {
		$result = CroogoNav::items('bogus');
		$this->assertEquals(array(), $result);
	}

/**
 * Test Get Menus
 */
	public function testNavGetMenus() {
		$result = CroogoNav::menus();
		$this->assertEquals(array('sidebar'), $result);

		CroogoNav::activeMenu('top');
		CroogoNav::add('foo', array('title' => 'foo'));

		$result = CroogoNav::menus();
		$this->assertEquals(array('sidebar', 'top'), $result);
	}

/**
 * Test multiple menu
 */
	public function testNavMultipleMenus() {
		CroogoNav::activeMenu('top');
		CroogoNav::add('foo', array('title' => 'foo'));

		$menus = array_keys(CroogoNav::items());
		$this->assertFalse(in_array('foo', $menus), 'foo exists in sidebar');

		$menus = array_keys(CroogoNav::items('top'));
		$this->assertTrue(in_array('foo', $menus), 'foo missing in top');
	}

	public function testNavMerge() {
		$foo = array('title' => 'foo', 'access' => array('public', 'admin'));
		$bar = array('title' => 'bar', 'access' => array('admin'));
		CroogoNav::clear();
		CroogoNav::add('foo', $foo);
		CroogoNav::add('foo', $bar);
		$items = CroogoNav::items();
		$expected = array('admin', 'public');
		sort($expected);
		sort($items['foo']['access']);
		$this->assertEquals($expected, $items['foo']['access']);
	}

	public function testNavOverwrite() {
		Croogo::dispatchEvent('Croogo.setupAdminData', null);
		$defaults = CroogoNav::getDefaults();

		$items = CroogoNav::items();
		$expected = Hash::merge($defaults, array(
			'title' => 'Permissions',
			'url' => array(
				'admin' => true,
				'plugin' => 'acl',
				'controller' => 'acl_permissions',
				'action' => 'index',
			),
			'weight' => 30,
		));
		$this->assertEquals($expected, $items['users']['children']['permissions']);

		$item = array(
			'title' => 'Permissions',
			'url' => array(
				'admin' => true,
				'plugin' => 'acl_extras',
				'controller' => 'acl_extras_permissions',
				'action' => 'index',
			),
			'weight' => 30,
		);
		CroogoNav::add('users.children.permissions', $item);
		$items = CroogoNav::items();

		$expected = Hash::merge($defaults, array(
			'title' => 'Permissions',
			'url' => array(
				'admin' => true,
				'plugin' => 'acl_extras',
				'controller' => 'acl_extras_permissions',
				'action' => 'index',
			),
			'weight' => 30,
		));

		$this->assertEquals($expected, $items['users']['children']['permissions']);
	}

}
