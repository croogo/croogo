<?php
App::uses('Menu', 'Model');
App::uses('AppModel', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

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
		'app.aco',
		'app.aro',
		'app.aros_aco',
		'app.menu',
		'app.link',
		'app.setting',
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

}