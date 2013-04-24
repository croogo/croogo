<?php
App::uses('TermsController', 'Taxonomy.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

class TermsControllerTest extends CroogoControllerTestCase {

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
		'plugin.nodes.node',
		'plugin.meta.meta',
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
			'View' => array(CakePlugin::path('Taxonomy') . 'View' . DS)
		), App::APPEND);
		$this->TermsController = $this->generate('Taxonomy.Terms', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Menus.Menus',
			),
		));
		$this->TermsController->Auth
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
		unset($this->TermsController);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/taxonomy/terms/index/1');
		$this->assertNotEmpty($this->vars['terms']);
		$expected = array(
			'1' => 'Uncategorized',
			'2' => 'Announcements',
		);
		$this->assertEquals($expected, $this->vars['termsTree']);
	}

/**
 * testAdminAdd
 *
 * @return void
 */
	public function testAdminAdd() {
		$this->expectFlashAndRedirect('Term saved successfuly.');
		$this->testAction('admin/taxonomy/terms/add/1', array(
			'data' => array(
				'Taxonomy' => array(
					'parent_id' => null,
				),
				'Term' => array(
					'title' => 'New Category',
					'slug' => 'new-category',
					'description' => 'category description here',
				),
			),
		));
		$termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
		$termsTreeSlugs = array_keys($termsTree);
		$expected = array(
			'uncategorized',
			'announcements',
			'new-category',
		);
		$this->assertEqual($termsTreeSlugs, $expected);
	}

/**
 * testAdminAddWithParent
 *
 * @return void
 */
	public function testAdminAddWithParent() {
		$this->expectFlashAndRedirect('Term saved successfuly.');
		$this->testAction('admin/taxonomy/terms/add/1', array(
			'data' => array(
				'Taxonomy' => array(
					'parent_id' => 1, // Uncategorized
				),
				'Term' => array(
					'title' => 'New Category',
					'slug' => 'new-category',
					'description' => 'category description here',
				),
			),
		));
		$termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
		$termsTreeTitles = array_values($termsTree);
		$expected = array(
			'Uncategorized',
			'_New Category',
			'Announcements',
		);
		$this->assertEqual($termsTreeTitles, $expected);
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->expectFlashAndRedirect('Term saved successfuly.');
		// ID of Uncategorized and Categories
		$this->testAction('/admin/taxonomy/terms/edit/1/1', array(
			'data' => array(
				'Taxonomy' => array(
					'parent_id' => null,
				),
				'Term' => array(
					'title' => 'New Category',
					'slug' => 'new-category',
					'description' => 'category description here',
				),
			),
		));
		$termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
		$expected = array(
			'new-category' => 'New Category',
			'announcements' => 'Announcements',
		);
		$this->assertEquals($expected, $termsTree);
	}

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->expectFlashAndRedirect('Term deleted');
		$this->testAction('admin/taxonomy/terms/delete/1/1'); // ID of Uncategorized and Categories
		$termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
		$expected = array(
			'announcements' => 'Announcements',
		);
		$this->assertEqual($termsTree, $expected);
	}

/**
 * testAdminMoveup
 *
 * @return void
 */
	public function testAdminMoveup() {
		$this->expectFlashAndRedirect('Moved up successfully');
		$this->testAction('admin/taxonomy/terms/moveup/2/1'); // ID of Announcements and Categories
		$termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
		$expected = array(
			'announcements' => 'Announcements',
			'uncategorized' => 'Uncategorized',
		);
		$this->assertEqual($termsTree, $expected);
	}

/**
 * testAdminMovedown
 *
 * @return void
 */
	public function testAdminMovedown() {
		$this->expectFlashAndRedirect('Moved down successfully');
		$this->testAction('admin/taxonomy/terms/movedown/1/1'); // ID of Uncategorized and Categories
		$termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
		$expected = array(
			'announcements' => 'Announcements',
			'uncategorized' => 'Uncategorized',
		);
		$this->assertEqual($termsTree, $expected);
	}

}
