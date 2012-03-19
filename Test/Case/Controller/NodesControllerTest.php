<?php
App::uses('NodesController', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class TestNodesController extends NodesController {

	public $name = 'Nodes';

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

	public function _stop($status = 0) {
		$this->stopped = $status;
	}

	public function __securityError() {

	}
}

class NodesControllerTest extends CroogoTestCase {

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

	public function startTest($method) {
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->Nodes = new TestNodesController($request, $response);
		$this->Nodes->constructClasses();
		$this->Nodes->request->params['controller'] = 'nodes';
		$this->Nodes->request->params['pass'] = array();
		$this->Nodes->request->params['named'] = array();
	}

	function testAdminIndex() {
		$this->Nodes->request->params['action'] = 'admin_index';
		$this->Nodes->request->params['url']['url'] = 'admin/nodes';
		$this->Nodes->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
		));
		$this->Nodes->startupProcess();
		$this->Nodes->admin_index();

		$this->Nodes->testView = true;
		$output = $this->Nodes->render('admin_index');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
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

	public function testAdminEdit() {
		$this->Nodes->request->params['action'] = 'admin_edit';
		$this->Nodes->request->params['url']['url'] = 'admin/nodes/edit';
		$this->Nodes->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
		));
		$this->Nodes->request->data = array(
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
		);
		$this->params['_Token']['key'] = 1;
		$this->Nodes->startupProcess();
		$this->Nodes->admin_edit(1);
		$this->assertEqual($this->Nodes->redirectUrl, array('action' => 'index'));

		$helloWorld = $this->Nodes->Node->findBySlug('hello-world');
		$this->assertEqual($helloWorld['Node']['title'], 'Hello World [modified]');

		//$this->Nodes->testView = true;
		//$output = $this->Nodes->render('admin_edit');
		//$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
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
	}

	public function endTest($method) {
		$this->Nodes->Session->destroy();
		unset($this->Nodes);
		ClassRegistry::flush();
	}
}
