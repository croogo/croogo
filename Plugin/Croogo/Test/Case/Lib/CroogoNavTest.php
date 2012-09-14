<?php
App::uses('CroogoNav', 'Croogo.Lib');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoNavTest extends CroogoTestCase {

	protected static $_menus = array();

	public function setUp() {
		parent::setUp();
		self::$_menus = CroogoNav::items();
	}

	public function tearDown() {
		parent::tearDown();
		CroogoNav::items(self::$_menus);
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

		CroogoNav::items($saved);
		$this->assertEquals($saved, CroogoNav::items());
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
