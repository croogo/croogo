<?php
App::uses('BlocksController', 'Blocks.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

class BlocksControllerTest extends CroogoControllerTestCase {

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
		$this->BlocksController = $this->generate('Blocks.Blocks', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->BlocksController->Auth
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
		unset($this->BlocksController);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/blocks/blocks/index');
		$this->assertNotEmpty($this->vars['blocks']);
	}

/**
 * testAdminIndexSearch
 *
 * @return void
 */
	public function testAdminIndexSearch() {
		$this->testAction('/admin/blocks/blocks/index?title=Recent');
		$this->assertNotEmpty($this->vars['blocks']);
		$this->assertEquals(1, count($this->vars['blocks']));
		$this->assertEquals(9, $this->vars['blocks'][0]['Block']['id']);
	}

/**
 * testAdminAdd
 *
 * @return void
 */
	public function testAdminAdd() {
		$this->expectFlashAndRedirect('The Block has been saved');
		$this->testAction('/admin/blocks/blocks/add', array(
			'data' => array(
				'Block' => array(
					'title' => 'Test block',
					'alias' => 'test_block',
					'class' => 'test-block',
					'show_title' => 'test_block',
					'region_id' => 4, // right
					'body' => 'text here',
					'visibility_paths' => '',
					'status' => 1,
				),
				'Role' => array(
					'Role' => array(),
				),
			),
		));
		$result = $this->BlocksController->Block->findByAlias('test_block');
		$this->assertEqual($result['Block']['title'], 'Test block');
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->expectFlashAndRedirect('The Block has been saved');
		$this->testAction('/admin/blocks/blocks/edit/1', array(
			'data' => array(
				'Block' => array(
					'id' => 3, // About
					'title' => 'About [modified]',
					'visibility_paths' => '',
				),
				'Role' => array(
					'Role' => array(),
				),
			),
		));
		$result = $this->BlocksController->Block->findByAlias('about');
		$this->assertEquals('About [modified]', $result['Block']['title']);
	}

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->expectFlashAndRedirect('Block deleted');
		$this->testAction('/admin/blocks/blocks/delete/8');
		$hasAny = $this->BlocksController->Block->hasAny(array(
			'Block.alias' => 'search',
		));
		$this->assertFalse($hasAny);
	}

/**
 * testAdminMoveUp
 *
 * @return void
 */
	public function testAdminMoveUp() {
		$this->expectFlashAndRedirect('Moved up successfully');
		$this->testAction('/admin/blocks/blocks/moveup/3');
		$list = $this->BlocksController->Block->find('list', array(
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Block.weight ASC',
		));
		$blockAliases = array_values($list);
		$this->assertEqual($blockAliases, array(
			'about',
			'search',
			'categories',
			'blogroll',
			'recent_posts',
			'meta',
		));
	}

/**
 * testAdminMoveUpWithSteps
 *
 * @return void
 */
	public function testAdminMoveUpWithSteps() {
		$this->expectFlashAndRedirect('Moved up successfully');
		$this->testAction('/admin/blocks/blocks/moveup/6/3');
		$list = $this->BlocksController->Block->find('list', array(
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Block.weight ASC',
		));
		$blockAliases = array_values($list);
		$this->assertEqual($blockAliases, array(
			'blogroll',
			'search',
			'about',
			'categories',
			'recent_posts',
			'meta',
		));
	}

/**
 * testAdminMoveDown
 *
 * @return void
 */
	public function testAdminMoveDown() {
		$this->expectFlashAndRedirect('Moved down successfully');
		$this->testAction('/admin/blocks/blocks/movedown/3');
		$list = $this->BlocksController->Block->find('list', array(
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Block.weight ASC',
		));
		$blockAliases = array_values($list);
		$this->assertEqual($blockAliases, array(
			'search',
			'categories',
			'about',
			'blogroll',
			'recent_posts',
			'meta',
		));
	}

/**
 * testAdminMoveDownWithSteps
 *
 * @return void
 */
	public function testAdminMoveDownWithSteps() {
		$this->expectFlashAndRedirect('Moved down successfully');
		$this->testAction('/admin/blocks/blocks/movedown/8/3');
		$list = $this->BlocksController->Block->find('list', array(
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Block.weight ASC',
		));
		$blockAliases = array_values($list);
		$this->assertEqual($blockAliases, array(
			'about',
			'categories',
			'blogroll',
			'search',
			'recent_posts',
			'meta',
		));
	}

/**
 * testAdminProcessDelete
 *
 * @return void
 */
	public function testAdminProcessDelete() {
		$this->expectFlashAndRedirect('Blocks deleted');
		$this->testAction('/admin/blocks/blocks/process', array(
			'data' => array(
				'Block' => array(
					'action' => 'delete',
					'8' => array('id' => 0), // Search
					'3' => array('id' => 1), // About
					'7' => array('id' => 0), // Categories
					'6' => array('id' => 1), // Blogroll
					'9' => array('id' => 0), // Recent Posts
					'5' => array('id' => 1), // Meta
				),
			),
		));
		$list = $this->BlocksController->Block->find('list', array(
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Block.weight ASC',
		));
		$blockAliases = array_values($list);
		$this->assertEqual($blockAliases, array(
			'search',
			'categories',
			'recent_posts',
		));
	}

/**
 * testAdminProcessPublish
 *
 * @return void
 */
	public function testAdminProcessPublish() {
		// unpublish a Block for testing
		$this->BlocksController->Block->id = 3; // About
		$this->BlocksController->Block->save(array(
			'id' => 3,
			'status' => false,
		));
		$this->BlocksController->Block->id = false;
		$about = $this->BlocksController->Block->hasAny(array(
			'id' => 3,
			'status' => 0,
		));
		$this->assertTrue($about);

		$this->expectFlashAndRedirect('Blocks published');

		$this->testAction('/admin/blocks/blocks/process', array(
			'data' => array(
				'Block' => array(
					'action' => 'publish',
					'8' => array('id' => 1), // Search
					'3' => array('id' => 1), // About
					'7' => array('id' => 1), // Categories
					'6' => array('id' => 1), // Blogroll
					'9' => array('id' => 1), // Recent Posts
					'5' => array('id' => 1), // Meta
				),
			),
		));

		$list = $this->BlocksController->Block->find('list', array(
			'conditions' => array(
				'Block.status' => true,
			),
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Block.weight ASC',
		));
		$blockAliases = array_values($list);
		$this->assertEqual($blockAliases, array(
			'search',
			'about',
			'categories',
			'blogroll',
			'recent_posts',
			'meta',
		));
	}

/**
 * testAdminProcessUnpublish
 *
 * @return void
 */
	public function testAdminProcessUnpublish() {
		$this->expectFlashAndRedirect('Blocks unpublished');
		$this->testAction('/admin/blocks/blocks/process', array(
			'data' => array(
				'Block' => array(
					'action' => 'unpublish',
					'8' => array('id' => 1), // Search
					'3' => array('id' => 1), // About
					'7' => array('id' => 0), // Categories
					'6' => array('id' => 1), // Blogroll
					'9' => array('id' => 0), // Recent Posts
					'5' => array('id' => 1), // Meta
				),
			),
		));

		$list = $this->BlocksController->Block->find('list', array(
			'conditions' => array(
				'Block.status' => 1,
			),
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Block.weight ASC',
		));
		$blockAliases = array_values($list);
		$this->assertEqual($blockAliases, array(
			'categories',
			'recent_posts',
		));
	}

}
