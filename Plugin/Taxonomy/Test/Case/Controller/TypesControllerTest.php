<?php
App::uses('TypesController', 'Taxonomy.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

class TypesControllerTest extends CroogoControllerTestCase {

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
		$this->TypesController = $this->generate('Taxonomy.Types', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->TypesController->Auth
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
		unset($this->TypesController);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/types/index');
		$this->assertNotEmpty($this->vars['displayFields']);
		$this->assertNotEmpty($this->vars['types']);
	}

/**
 * testAdminAdd
 *
 * @return void
 */
	public function testAdminAdd() {
		$this->expectFlashAndRedirect('The Type has been saved');
		$this->testAction('admin/taxonomy/types/add', array(
			'data' => array(
				'Type' => array(
					'title' => 'New Type',
					'alias' => 'new_type',
					'description' => 'A new type',
				),
			),
		));
		$newType = $this->TypesController->Type->findByAlias('new_type');
		$this->assertEqual($newType['Type']['title'], 'New Type');
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->expectFlashAndRedirect('The Type has been saved');
		$this->testAction('/admin/types/edit/1', array(
			'data' => array(
				'Type' => array(
					'id' => 1, // page
					'description' => '[modified]',
				),
			),
		));
		$page = $this->TypesController->Type->findByAlias('page');
		$this->assertEquals('[modified]', $page['Type']['description']);
	}

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->expectFlashAndRedirect('Type deleted');
		$this->testAction('/admin/types/delete/1'); // ID of page
		$hasAny = $this->TypesController->Type->hasAny(array(
			'Type.alias' => 'page',
		));
		$this->assertFalse($hasAny);
	}

}
