<?php
App::uses('VocabulariesController', 'Taxonomy.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

/**
 * VocabulariesController Test
 */
class VocabulariesControllerTest extends CroogoControllerTestCase {

/**
 * fixtures
 */
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
		$this->VocabulariesController = $this->generate('Taxonomy.Vocabularies', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->VocabulariesController->Auth
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
		unset($this->VocabulariesController);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/taxonomy/vocabularies/index');
		$this->assertNotEmpty($this->vars['vocabularies']);
	}

/**
 * testAdminAdd
 *
 * @return void
 */
	public function testAdminAdd() {
		$this->expectFlashAndRedirect('The Vocabulary has been saved');
		$this->testAction('admin/taxonomy/vocabularies/add', array(
			'data' => array(
				'Vocabulary' => array(
					'title' => 'New Vocabulary',
					'alias' => 'new_vocabulary',
				),
			),
		));
		$newVocabulary = $this->VocabulariesController->Vocabulary->findByAlias('new_vocabulary');
		$this->assertEqual($newVocabulary['Vocabulary']['title'], 'New Vocabulary');
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->expectFlashAndRedirect('The Vocabulary has been saved');
		$this->testAction('/admin/taxonomy/vocabularies/edit/1', array(
			'data' => array(
				'Vocabulary' => array(
					'id' => 1, // categories
					'title' => 'Categories [modified]',
				),
			),
		));
		$categories = $this->VocabulariesController->Vocabulary->findByAlias('categories');
		$this->assertEquals('Categories [modified]', $categories['Vocabulary']['title']);
	}

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->expectFlashAndRedirect('Vocabulary deleted');
		$this->testAction('admin/taxonomy/vocabularies/delete/1'); // ID of categories
		$hasAny = $this->VocabulariesController->Vocabulary->hasAny(array(
			'Vocabulary.alias' => 'categories',
		));
		$this->assertFalse($hasAny);
	}

/**
 * testAdminMoveup
 *
 * @return void
 */
	public function testAdminMoveup() {
		$this->expectFlashAndRedirect('Moved up successfully');
		$this->testAction('admin/taxonomy/vocabularies/moveup/2'); // ID of tags
		$vocabularies = $this->VocabulariesController->Vocabulary->find('list', array(
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Vocabulary.weight ASC',
		));
		$expected = array(
			'2' => 'tags',
			'1' => 'categories',
		);
		$this->assertEqual($vocabularies, $expected);
	}

/**
 * testAdminMovedown
 *
 * @return void
 */
	public function testAdminMovedown() {
		$this->expectFlashAndRedirect('Moved down successfully');
		$this->testAction('admin/taxonomy/vocabularies/movedown/1'); // ID of categories
		$vocabularies = $this->VocabulariesController->Vocabulary->find('list', array(
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Vocabulary.weight ASC',
		));
		$expected = array(
			'2' => 'tags',
			'1' => 'categories',
		);
		$this->assertEqual($vocabularies, $expected);
	}

}
