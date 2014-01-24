<?php
App::uses('Menu', 'Menus.Model');
App::uses('MenusAppModel', 'Menus.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

/**
 * TestMenu
 *
 */
class TestMenu extends Menu {

/**
 * model alias
 *
 * @var string
 */
	public $alias = 'Menu';

}

/**
 * TestUser
 *
 */
class MenuTest extends CroogoTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
		'plugin.settings.setting',
		'plugin.menus.menu',
		'plugin.menus.link',
	);

/**
 * Menu instance
 *
 * @var TestMenu
 */
	public $Menu;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Menu = ClassRegistry::init('TestMenu');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Menu);
	}

/**
 * testDeleteDependentLinks method
 */
	public function testDeleteDependentLinks() {
		$totalLinks = $this->Menu->Link->find('count');

		$toDelete = $this->Menu->Link->find('count', array(
			'conditions' => array('Link.menu_id' => 4)
			));
		$this->assertTrue($toDelete > 0);

		$this->Menu->id = 4;
		$this->Menu->delete();

		$count = $this->Menu->Link->find('count', array('conditions' => array('Link.menu_id' => 4)));
		$this->assertTrue($count == 0);

		$currentLinks = $this->Menu->Link->find('count');
		$this->assertEquals($totalLinks, $currentLinks + $toDelete);
	}

/**
 * Test deleting a menu should not mess up other menu Link hierarchy
 */
	public function testDeleteMenuLinkIntegrity() {
		$settings = array('scope' => array('Link.menu_id' => 3));

		$expected = array(
			7 => 'Home',
			8 => 'About',
			9 => '_Child link',
			15 => 'Contact'
		);

		$this->Menu->Link->Behaviors->Tree->setup($this->Menu->Link, $settings);
		$links = $this->Menu->Link->generateTreeList(array('menu_id' => 3));
		$this->assertEquals($expected, $links);

		$this->Menu->delete(6);

		$this->Menu->Link->Behaviors->Tree->setup($this->Menu->Link, $settings);
		$links = $this->Menu->Link->generateTreeList(array('menu_id' => 3));
		$this->assertEquals($expected, $links);
	}

}