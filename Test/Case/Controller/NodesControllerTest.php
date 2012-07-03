<?php
App::uses('NodesController', 'Controller');
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
		'block',
		'comment',
		'contact',
		'i18n',
		'language',
		'link',
		'menu',
		'message',
		'meta',
		'node',
		'nodes_taxonomy',
		'region',
		'role',
		'setting',
		'taxonomy',
		'term',
		'type',
		'types_vocabulary',
		'user',
		'vocabulary',
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
		$this->Nodes = new TestNodesController($request, $response);
		$this->Nodes->constructClasses();
		$this->Nodes->Security = $this->getMock('SecurityComponent', null, array($this->Nodes->Components));
		$this->Nodes->request->params['controller'] = 'nodes';
		$this->Nodes->request->params['pass'] = array();
		$this->Nodes->request->params['named'] = array();

		$this->NodesController = $this->generate('Nodes', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->NodesController->Auth
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
		unset($this->Nodes);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/nodes/index');
		$this->assertNotEmpty($this->vars['nodes']);
	}

	public function testAdminAdd() {
		$this->Nodes->request->params['action'] = 'admin_add';
		$this->Nodes->request->params['url']['url'] = 'admin/nodes/add';
		$this->Nodes->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
		));
		$this->Nodes->request->data = array(
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
		);
		$this->Nodes->request->params['_Token']['key'] = 1;
		$this->Nodes->startupProcess();
		$this->Nodes->admin_add();
		$this->assertEqual($this->Nodes->redirectUrl, array('action' => 'index'));

		$newBlog = $this->Nodes->Node->findBySlug('new-blog');
		$this->assertEqual($newBlog['Node']['title'], 'New Blog');

		$this->Nodes->testView = true;
		$output = $this->Nodes->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->NodesController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('Blog has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->NodesController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/nodes/edit/1', array(
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

	public function testAdminDelete() {
		$this->Nodes->request->params['action'] = 'admin_delete';
		$this->Nodes->request->params['url']['url'] = 'admin/nodes/delete';
		$this->Nodes->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
		));
		$this->Nodes->startupProcess();
		$this->Nodes->admin_delete(1); // ID of Hello World
		$this->assertEqual($this->Nodes->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Nodes->Node->hasAny(array(
			'Node.slug' => 'hello-world',
		));
		$this->assertFalse($hasAny);
		$this->assertFalse($this->Nodes->blackholed);
	}

	public function testBlackholedRequest() {
		$request = new CakeRequest('/admin/nodes/delete/1');
		$response = new CakeResponse();
		$this->Nodes = new TestNodesController($request, $response);
		$this->Nodes->constructClasses();
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

	public function testViewFallback() {
		$this->Nodes->theme = 'Mytheme';
		$result = $this->Nodes->viewFallback(array('index_blog'));
		$this->assertContains('index_blog.ctp in Mytheme', $result->body());

		$result = $this->Nodes->viewFallback(array('view_1', 'view_blog'));
		$this->assertContains('view_1.ctp in Mytheme', $result->body());
	}

}
