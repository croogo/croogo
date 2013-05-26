<?php
App::uses('MenusController', 'Menus.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

class MenusControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
		'plugin.blocks.block',
		'plugin.comments.comment',
		'plugin.contacts.contact',
		'plugin.translate.i18n',
		'plugin.settings.language',
		'plugin.contacts.message',
		'plugin.nodes.node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'plugin.settings.setting',
		'plugin.menus.menu',
		'plugin.menus.link',
		'plugin.meta.meta',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.term',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.users.user',
		'plugin.taxonomy.vocabulary',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MenusController = $this->generate('Menus.Menus', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->MenusController->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnCallback(array($this, 'authUserCallback')));
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->MenusController);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/menus/menus/index');
		$this->assertNotEmpty($this->vars['menus']);
	}

/**
 * testAdminAdd
 *
 * @return void
 */
	public function testAdminAdd() {
		$this->expectFlashAndRedirect('The Menu has been saved');
		$mainMenu = ClassRegistry::init('Menus.Menu')->findByAlias('main');
		$this->testAction('/admin/menus/menus/add', array(
			'data' => array(
				'Menu' => array(
					'title' => 'New Menu',
					'description' => 'A new menu',
					'alias' => 'new',
					'link_count' => 0,
				),
			),
		));
		$newMenu = $this->MenusController->Menu->findByAlias('new');
		$this->assertEqual($newMenu['Menu']['title'], 'New Menu');
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->expectFlashAndRedirect('The Menu has been saved');
		$this->testAction('/admin/menus/menus/edit/1', array(
			'data' => array(
				'Menu' => array(
					'id' => 3, // main
					'title' => 'Main Menu [modified]',
				),
			),
		));
		$result = $this->MenusController->Menu->findByAlias('main');
		$this->assertEquals('Main Menu [modified]', $result['Menu']['title']);
	}

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->expectFlashAndRedirect('Menu deleted');
		$this->testAction('/admin/menus/menus/delete/4');
		$hasAny = $this->MenusController->Menu->hasAny(array(
			'Menu.alias' => 'footer',
		));
		$this->assertFalse($hasAny);
	}

}
