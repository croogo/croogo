<?php
App::uses('BlocksController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

class TestBlocksController extends BlocksController {

	public $name = 'Blocks';

	public $autoRender = false;

	public $testView = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	public function render($action = null, $layout = null, $file = null) {
		if (!$this->testView) {
			$this->renderedAction = $action;
		} else {
			return parent::render($action, $layout, $file);
		}
	}

	protected function _stop($status = 0) {
		$this->stopped = $status;
	}

	public function securityError($type) {
	}

}

class BlocksControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'app.aco',
		'app.aro',
		'app.aros_aco',
		'app.block',
		'app.comment',
		'app.contact',
		'app.i18n',
		'app.language',
		'app.link',
		'app.menu',
		'app.message',
		'app.meta',
		'app.node',
		'app.nodes_taxonomy',
		'app.region',
		'app.role',
		'app.setting',
		'app.taxonomy',
		'app.term',
		'app.type',
		'app.types_vocabulary',
		'app.user',
		'app.vocabulary',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->Blocks = new TestBlocksController($request, $response);
		$this->Blocks->constructClasses();
		$this->Blocks->Security = $this->getMock('SecurityComponent', null, array($this->Blocks->Components));
		$request->params['controller'] = 'blocks';
		$request->params['pass'] = $request->params['named'] = array();

		$this->BlocksController = $this->generate('Blocks', array(
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
		unset($this->Blocks);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/blocks/index');
		$this->assertNotEmpty($this->vars['blocks']);
	}

	public function testAdminAdd() {
		$this->Blocks->request->params['action'] = 'admin_add';
		$this->Blocks->request->params['url']['url'] = 'admin/blocks/add';
		$this->Blocks->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Blocks->request->data = array(
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
		);
		$this->Blocks->startupProcess();
		$this->Blocks->admin_add();
		$this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));

		$testBlock = $this->Blocks->Block->findByAlias('test_block');
		$this->assertEqual($testBlock['Block']['title'], 'Test block');

		$this->Blocks->testView = true;
		$output = $this->Blocks->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->BlocksController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Block has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->BlocksController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/blocks/edit/1', array(
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
		$result = $this->Blocks->Block->findByAlias('about');
		$this->assertEquals('About [modified]', $result['Block']['title']);
	}

	public function testAdminDelete() {
		$this->Blocks->request->params['action'] = 'admin_delete';
		$this->Blocks->request->params['url']['url'] = 'admin/blocks/delete';
		$this->Blocks->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Blocks->startupProcess();
		$this->Blocks->admin_delete(8); // ID of Search
		$this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Blocks->Block->hasAny(array(
			'Block.alias' => 'search',
		));
		$this->assertFalse($hasAny);
	}

	public function testAdminMoveUp() {
		$this->Blocks->request->params['action'] = 'admin_moveup';
		$this->Blocks->request->params['url']['url'] = 'admin/blocks/moveup/3';
		$this->Blocks->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Blocks->startupProcess();

		$this->Blocks->admin_moveup(3); // About
		$this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
		$list = $this->Blocks->Block->find('list', array(
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

	public function testAdminMoveUpWithSteps() {
		$this->Blocks->request->params['action'] = 'admin_moveup';
		$this->Blocks->request->params['url']['url'] = 'admin/blocks/moveup/6/3';
		$this->Blocks->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Blocks->startupProcess();

		$this->Blocks->admin_moveup(6, 3); // Blogroll up 3 steps
		$this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
		$list = $this->Blocks->Block->find('list', array(
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

	public function testAdminMoveDown() {
		$this->Blocks->request->params['action'] = 'admin_movedown';
		$this->Blocks->request->params['url']['url'] = 'admin/blocks/movedown/3';
		$this->Blocks->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Blocks->startupProcess();

		$this->Blocks->admin_movedown(3); // About
		$this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
		$list = $this->Blocks->Block->find('list', array(
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

	public function testAdminMoveDownWithSteps() {
		$this->Blocks->request->params['action'] = 'admin_movedown';
		$this->Blocks->request->params['url']['url'] = 'admin/blocks/movedown/8/3';
		$this->Blocks->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Blocks->startupProcess();

		$this->Blocks->admin_movedown(8, 2); // Search down 2 steps
		$this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
		$list = $this->Blocks->Block->find('list', array(
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
			'search',
			'blogroll',
			'recent_posts',
			'meta',
		));
	}

	public function testAdminProcessDelete() {
		$this->Blocks->request->params['action'] = 'admin_process';
		$this->Blocks->request->params['url']['url'] = 'admin/blocks/process';
		$this->Blocks->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));

		$this->Blocks->request->data['Block'] = array(
			'action' => 'delete',
			'8' => array( // Search
				'id' => 0,
			),
			'3' => array( // About
				'id' => 1,
			),
			'7' => array( // Categories
				'id' => 0,
			),
			'6' => array( // Blogroll
				'id' => 1,
			),
			'9' => array( // Recent Posts
				'id' => 0,
			),
			'5' => array( // Meta
				'id' => 1,
			),
		);
		$this->Blocks->startupProcess();
		$this->Blocks->admin_process();
		$this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
		$list = $this->Blocks->Block->find('list', array(
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

	public function testAdminProcessPublish() {
		$this->Blocks->request->params['action'] = 'admin_process';
		$this->Blocks->request->params['url']['url'] = 'admin/blocks/process';
		$this->Blocks->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Blocks->startupProcess();

		// unpublish a Block for testing
		$this->Blocks->Block->id = 3; // About
		$this->Blocks->Block->save(array(
			'id' => 3,
			'status' => false,
		));
		$this->Blocks->Block->id = false;
		$about = $this->Blocks->Block->hasAny(array(
			'id' => 3,
			'status' => 0,
		));
		$this->assertTrue($about);

		$this->Blocks->request->data['Block'] = array(
			'action' => 'publish',
			'8' => array( // Search
				'id' => 1,
			),
			'3' => array( // About
				'id' => 1,
			),
			'7' => array( // Categories
				'id' => 1,
			),
			'6' => array( // Blogroll
				'id' => 1,
			),
			'9' => array( // Recent Posts
				'id' => 1,
			),
			'5' => array( // Meta
				'id' => 1,
			),
		);
		$this->Blocks->admin_process();
		$this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
		$list = $this->Blocks->Block->find('list', array(
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

	public function testAdminProcessUnpublish() {
		$this->Blocks->request->params['action'] = 'admin_process';
		$this->Blocks->request->params['url']['url'] = 'admin/blocks/process';
		$this->Blocks->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Blocks->startupProcess();

		$this->Blocks->request->data['Block'] = array(
			'action' => 'unpublish',
			'8' => array( // Search
				'id' => 1,
			),
			'3' => array( // About
				'id' => 1,
			),
			'7' => array( // Categories
				'id' => 0,
			),
			'6' => array( // Blogroll
				'id' => 1,
			),
			'9' => array( // Recent Posts
				'id' => 0,
			),
			'5' => array( // Meta
				'id' => 1,
			),
		);
		$this->Blocks->admin_process();
		$this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
		$list = $this->Blocks->Block->find('list', array(
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
