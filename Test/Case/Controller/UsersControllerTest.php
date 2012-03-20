<?php
App::uses('UsersController', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class TestUsersController extends UsersController {

	public $name = 'Users';

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

	public function __securityError($type) {

	}
}

class UsersControllerTest extends CroogoTestCase {

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
		$this->Users = new TestUsersController($request, $response);
		$this->Users->constructClasses();
		$this->Users->User->Aro->useDbConfig = $this->Users->User->useDbConfig;
		$this->Users->request->params['controller'] = 'users';
		$this->Users->request->params['pass'] = array();
		$this->Users->request->params['named'] = array();
	}

	function testAdminIndex() {
		$this->Users->request->params['action'] = 'admin_index';
		$this->Users->request->params['url']['url'] = 'admin/users';
		$this->Users->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Users->startupProcess();
		$this->Users->admin_index();

		$this->Users->testView = true;
		$output = $this->Users->render('admin_index');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

	public function testAdminAdd() {
		$this->Users->request->params['action'] = 'admin_add';
		$this->Users->request->params['url']['url'] = 'admin/users/add';
		$this->Users->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Users->request->data = array(
			'User' => array(
				'username' => 'new_user',
				'password' => uniqid(),
				'email' => 'new_user@croogo.dev',
				'name' => 'New User',
				'role_id' => 3,
			),
		);
		$this->Users->startupProcess();
		$this->Users->admin_add();
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'index'));

		$newUser = $this->Users->User->findByUsername('new_user');
		$this->assertEqual($newUser['User']['name'], 'New User');

		$this->Users->testView = true;
		$output = $this->Users->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

	public function testAdminEdit() {
		$this->Users->request->params['action'] = 'admin_edit';
		$this->Users->request->params['url']['url'] = 'admin/users/edit';
		$this->Users->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Users->request->data = array(
			'User' => array(
				'id' => 1, // admin
				'name' => 'Administrator [modified]',
				'role_id' => 1,
			),
		);
		$this->Users->startupProcess();
		$this->Users->admin_edit(1);
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'index'));

		$admin = $this->Users->User->findByUsername('admin');
		$this->assertEqual($admin['User']['name'], 'Administrator [modified]');

		$this->Users->testView = true;
		$this->Users->request->params['pass']['0'] = 1;
		$output = $this->Users->render('admin_edit');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

	public function testAdminDelete() {
		$this->Users->request->params['action'] = 'admin_delete';
		$this->Users->request->params['url']['url'] = 'admin/users/delete';
		$this->Users->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Users->startupProcess();

		// delete another user
		$this->Users->admin_delete(2); // ID of rchavik
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Users->User->hasAny(array(
			'User.username' => 'rchavik',
		));
		$this->assertFalse($hasAny);

		// delete the only remaining admin
		$this->Users->admin_delete(1); // ID of admin
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'index'));
		$hasAny = $this->Users->User->hasAny(array(
			'User.username' => 'admin',
		));
		$this->assertTrue($hasAny);
	}

	public function endTest($method) {
		$this->Users->Session->destroy();
		unset($this->Users);
		ClassRegistry::flush();
	}
}
