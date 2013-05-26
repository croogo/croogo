<?php
App::uses('RegionsController', 'Blocks.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

class RegionsControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
		'plugin.blocks.block',
		'plugin.comments.comment',
		'plugin.contacts.contact',
		'plugin.translate.i18n',
		'plugin.settings.language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'plugin.contacts.message',
		'plugin.meta.meta',
		'plugin.nodes.node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'plugin.settings.setting',
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
		App::build(array(
			'View' => array(CakePlugin::path('Blocks') . 'View' . DS)
		), App::APPEND);
		$this->RegionsController = $this->generate('Blocks.Regions', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->RegionsController->Auth
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
		unset($this->RegionsController);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/blocks/regions/index');
		$this->assertNotEmpty($this->vars['displayFields']);
		$this->assertNotEmpty($this->vars['regions']);
	}

/**
 * testAdminAdd
 *
 * @return void
 */
	public function testAdminAdd() {
		$this->expectFlashAndRedirect('The Region has been saved');
		$this->testAction('/admin/blocks/regions/add', array(
			'data' => array(
				'Region' => array(
					'title' => 'new_region',
					'alias' => 'new_region',
					'description' => 'A new region',
				),
			),
		));
		$newRegion = $this->RegionsController->Region->findByAlias('new_region');
		$this->assertEqual($newRegion['Region']['title'], 'new_region');
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->expectFlashAndRedirect('The Region has been saved');
		$this->testAction('/admin/blocks/regions/edit/1', array(
			'data' => array(
				'Region' => array(
					'id' => 4, // right
					'title' => 'right_modified',
				),
			),
		));
		$right = $this->RegionsController->Region->findByAlias('right');
		$this->assertEquals('right_modified', $right['Region']['title']);
	}

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->expectFlashAndRedirect('Region deleted');
		$this->testAction('/admin/blocks/regions/delete/4');
		$hasAny = $this->RegionsController->Region->hasAny(array(
			'Region.alias' => 'right',
		));
		$this->assertFalse($hasAny);
	}

}
