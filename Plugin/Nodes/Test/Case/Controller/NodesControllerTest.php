<?php
App::uses('NodesController', 'Nodes.Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

class TestNodesController extends NodesController {

	public $name = 'Nodes';

	public $autoRender = false;

	public $testView = false;

	public $blackholed = false;

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

	public function viewFallback($views) {
		$this->testView = true;
		return $this->_viewFallback($views);
	}

	public function securityError($type) {
		$this->blackholed = true;
	}

}

class NodesControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
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
		$this->NodesController = $this->generate('Nodes.Nodes', array(
			'methods' => array(
				'redirect',
				'is',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security' => array('blackHole'),
			),
		));
		$this->NodesController->Node->Behaviors->detach('Acl');
		$this->NodesController->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnCallback(array($this, 'authUserCallback')));
		$this->NodesController->Security->Session = $this->getMock('CakeSession');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->NodesController);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/nodes/index');
		$this->assertNotEmpty($this->vars['nodes']);
		$this->assertNotEmpty($this->vars['nodes'][0]['Node']);
		$this->assertNotEmpty($this->vars['nodes'][0]['User']);
		$this->assertArrayHasKey('CustomFields', $this->vars['nodes'][0]);
	}

/**
 * testAdminAdd
 *
 * @return void
 */
	public function testAdminAdd() {
		$this->expectFlashAndRedirect('Node has been saved');
		$this->testAction('/admin/nodes/nodes/add', array(
			'data' => array(
				'Node' => array(
					'title' => 'New Blog',
					'slug' => 'new-blog',
					'type' => 'blog',
					'token_key' => 1,
					'body' => '',
				),
				'Role' => array(
					'Role' => array(),
				),
			),
		));
		$newBlog = $this->NodesController->Node->findBySlug('new-blog');
		$this->assertEqual($newBlog['Node']['title'], 'New Blog');
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->expectFlashAndRedirect('Blog has been saved');
		$this->testAction('/admin/nodes/nodes/edit/1', array(
			'data' => array(
				'Node' => array(
					'id' => 1,
					'title' => 'Hello World [modified]',
					'slug' => 'hello-world',
					'type' => 'blog',
					'token_key' => 1,
				),
				'Role' => array(
					'Role' => array(),
				),
			),
		));
		$result = $this->NodesController->Node->findBySlug('hello-world');
		$this->assertEquals('Hello World [modified]', $result['Node']['title']);
	}

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->expectFlashAndRedirect('Node deleted');
		$this->NodesController->Security
			->expects($this->never())
			->method('blackHole');
		$this->testAction('/admin/nodes/nodes/delete/1');
		$hasAny = $this->NodesController->Node->hasAny(array(
			'Node.slug' => 'hello-world',
		));
		$this->assertFalse($hasAny);
	}

/**
 * testBlackholedRequest
 *
 * @return void
 */
	public function testBlackholedRequest() {
		$request = new CakeRequest('/admin/nodes/nodes/delete/1');
		$response = new CakeResponse();
		$this->Nodes = new TestNodesController($request, $response);
		$this->Nodes->constructClasses();
		$this->Nodes->request->params['plugin'] = 'nodes';
		$this->Nodes->request->params['controller'] = 'nodes';
		$this->Nodes->request->params['action'] = 'admin_delete';
		$this->Nodes->request->params['prefix'] = 'admin';
		$this->Nodes->request->params['pass'] = array();
		$this->Nodes->request->params['named'] = array();
		$this->Nodes->startupProcess();
		$this->Nodes->Node->Behaviors->detach('Tree');
		$this->Nodes->invokeAction($request);
		$this->assertTrue($this->Nodes->blackholed);
		$hasAny = $this->Nodes->Node->hasAny(array(
			'Node.id' => 1,
		));
		$this->assertTrue($hasAny);
	}

/**
 * testViewFallback
 *
 * @return void
 */
	public function testViewFallback() {
		$request = new CakeRequest('/admin/nodes/nodes/delete/1');
		$response = new CakeResponse();
		$this->Nodes = new TestNodesController($request, $response);
		$this->Nodes->constructClasses();
		$this->Nodes->startupProcess();
		$this->Nodes->Node->Behaviors->detach('Tree');

		$this->Nodes->theme = 'Mytheme';
		$result = $this->Nodes->viewFallback(array('index_blog'));
		$this->assertContains('index_blog.ctp in Mytheme', $result->body());

		$result = $this->Nodes->viewFallback(array('view_1', 'view_blog'));
		$this->assertContains('view_1.ctp in Mytheme', $result->body());
	}

}
