<?php
App::uses('NodesController', 'Nodes.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

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
		$this->assertEquals(2, count($this->vars['nodes']));
		$this->assertNotEmpty($this->vars['nodes'][0]['Node']);
		$this->assertNotEmpty($this->vars['nodes'][0]['User']);
		$this->assertArrayHasKey('CustomFields', $this->vars['nodes'][0]);
	}

/**
 * testAdminIndexSearch
 *
 * @return void
 */
	public function testAdminIndexSearch() {
		$this->testAction('/admin/nodes/index?filter=about');
		$this->assertEquals(1, count($this->vars['nodes']));
		$this->assertEquals(2, $this->vars['nodes'][0]['Node']['id']);
		$this->assertArrayHasKey('CustomFields', $this->vars['nodes'][0]);
	}

/**
 * testAdminIndex - from popups
 *
 * @return void
 */
	public function testAdminLinks() {
		$this->testAction('/admin/nodes/nodes/index/links:1/filter:about');
		$this->assertEquals('admin_popup', $this->controller->View->layout);
		$this->assertNotEmpty($this->vars['nodes']);
		$this->assertNotEmpty($this->vars['nodes'][0]['Node']);
		$this->assertEquals('about', $this->vars['nodes'][0]['Node']['slug']);
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
					'title' => 'New Node',
					'slug' => 'new-node',
					'token_key' => 1,
					'body' => '',
					'created' => '',
				),
				'Role' => array(
					'Role' => array(),
				),
			),
		));
		$newBlog = $this->NodesController->Node->findBySlug('new-node');
		$this->assertEqual($newBlog['Node']['title'], 'New Node');
		$this->assertNotEmpty($newBlog['Node']['created']);
		$this->assertEqual($newBlog['Node']['type'], 'node');
		$this->assertNotEquals('0000-00-00 00:00:00', $newBlog['Node']['created']);
	}

/**
 * testAdminAddBlog
 *
 * @return void
 */
	public function testAdminAddBlog() {
		$this->expectFlashAndRedirect('Blog has been saved');
		$this->testAction('/admin/nodes/nodes/add/blog', array(
			'data' => array(
				'Node' => array(
					'title' => 'New Blog',
					'slug' => 'new-blog',
					'token_key' => 1,
					'body' => '',
					'created' => '',
				),
				'Role' => array(
					'Role' => array(),
				),
			),
		));
		$newBlog = $this->NodesController->Node->findBySlug('new-blog');
		$this->assertEqual($newBlog['Node']['title'], 'New Blog');
		$this->assertNotEmpty($newBlog['Node']['created']);
		$this->assertEqual($newBlog['Node']['type'], 'blog');
		$this->assertNotEquals('0000-00-00 00:00:00', $newBlog['Node']['created']);
	}

/**
 * testAdminAddCustomCreated
 *
 * @return void
 */
	public function testAdminAddCustomCreated() {
		$this->expectFlashAndRedirect('Node has been saved');
		$title = 'New Blog (custom created value)';
		$slug = 'new-blog-custom-created-value';
		$this->testAction('/admin/nodes/nodes/add', array(
			'data' => array(
				'Node' => array(
					'title' => $title,
					'slug' => $slug,
					'type' => 'blog',
					'token_key' => 1,
					'body' => '',
					'created' => '2012-03-24 01:02:03',
				),
				'Role' => array(
					'Role' => array(),
				),
			),
		));
		$newBlog = $this->NodesController->Node->findBySlug($slug);
		$this->assertEqual($newBlog['Node']['title'], $title);
		$this->assertNotEmpty($newBlog['Node']['created'], '2012-03-24 01:02:03');
	}

/**
 * testAdminProcessWithInvalidAction
 *
 * @return void
 */
	public function testAdminProcessWithInvalidAction() {
		$this->setExpectedException('InvalidArgumentException');
		$this->testAction('/admin/nodes/nodes/process', array(
			'data' => array(
				'Node' => array(
					'action' => 'avadakadavra',
					'1' => array('id' => 0),
					'2' => array('id' => 1),
				),
			),
		));
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
		App::build(array(
			'View' => array(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'View' . DS . 'Themed' . DS . 'Mytheme' . DS),
		), App::PREPEND);

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

/**
 * testViewFallback from plugin controller that extends NodesController
 *
 * @return void
 */
	public function testViewFallbackInPlugins() {
		CakePlugin::load('TestPlugin');
		$this->Nodes = $this->getMock('TestNodesController',
			array('render'), array(new CakeRequest(), new CakeResponse())
		);
		$this->Nodes->theme = null;
		$this->Nodes->plugin = 'TestPlugin';
		$this->Nodes
			->expects($this->once())
			->method('render')
			->with(
				$this->equalTo('index_event')
			);
		$this->Nodes->viewFallback(array('index_event'));
		unset($this->Nodes);
	}

/**
 * testViewFallback from plugin controller that extends NodesController
 * with an active theme
 *
 * @return void
 */
	public function testViewFallbackInPluginsWithTheme() {
		CakePlugin::load('TestPlugin');
		$this->Nodes = $this->getMock('TestNodesController',
			array('render'), array(new CakeRequest(), new CakeResponse())
		);
		$this->Nodes->theme = 'Mytheme';
		$this->Nodes->plugin = 'TestPlugin';
		$this->Nodes
			->expects($this->once())
			->method('render')
			->with(
				$this->equalTo('index_blog')
			);
		$this->Nodes->viewFallback(array('index_blog'));
		unset($this->Nodes);
	}

/**
 * testViewFallback correctly use views from Nodes plugin
 *
 * @return void
 */
	public function testViewFallbackToCorePlugins() {
		CakePlugin::load('TestPlugin');
		$this->Nodes = $this->getMock('TestNodesController',
			array('render'), array(new CakeRequest(), new CakeResponse())
		);
		$this->Nodes->theme = null;
		$this->Nodes->plugin = 'TestPlugin';
		$this->Nodes
			->expects($this->never())
			->method('render');
		$this->Nodes->viewFallback(array('view_1', 'view_blog'));
		unset($this->Nodes);
	}

}
