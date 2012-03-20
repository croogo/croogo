<?php
App::uses('MenusController', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class TestMenusController extends MenusController {

	public $name = 'Menus';

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

class MenusControllerTest extends CroogoTestCase {

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
		$this->Menus = new TestMenusController($request, $response);
		$this->Menus->constructClasses();
		$this->Menus->request->params['controller'] = 'menus';
		$this->Menus->request->params['pass'] = array();
		$this->Menus->request->params['named'] = array();
	}

	public function testAdminIndex() {
		$this->Menus->request->params['action'] = 'admin_index';
		$this->Menus->request->params['url']['url'] = 'admin/menus';
		$this->Menus->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Menus->startupProcess();
		$this->Menus->admin_index();

		$this->Menus->testView = true;
		$output = $this->Menus->render('admin_index');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

	public function testAdminAdd() {
		$this->Menus->request->params['action'] = 'admin_add';
		$this->Menus->request->params['url']['url'] = 'admin/menus/add';
		$this->Menus->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Menus->request->data = array(
			'Menu' => array(
				'title' => 'New Menu',
				'description' => 'A new menu',
				'alias' => 'new',
				'link_count' => 0,
			),
		);
		$this->Menus->startupProcess();
		$this->Menus->admin_add();
		$this->assertEqual($this->Menus->redirectUrl, array('action' => 'index'));

		$newMenu = $this->Menus->Menu->findByAlias('new');
		$this->assertEqual($newMenu['Menu']['title'], 'New Menu');

		$this->Menus->testView = true;
		$output = $this->Menus->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

	public function testAdminEdit() {
		$this->Menus->request->params['action'] = 'admin_edit';
		$this->Menus->request->params['url']['url'] = 'admin/menus/edit';
		$this->Menus->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Menus->request->data = array(
			'Menu' => array(
				'id' => 3, // main
				'title' => 'Main Menu [modified]',
			),
		);
		$this->Menus->startupProcess();
		$this->Menus->admin_edit();
		$this->assertEqual($this->Menus->redirectUrl, array('action' => 'index'));

		$main = $this->Menus->Menu->findByAlias('main');
		$this->assertEqual($main['Menu']['title'], 'Main Menu [modified]');

		$this->Menus->testView = true;
		$output = $this->Menus->render('admin_edit');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

	public function testAdminDelete() {
		$this->Menus->request->params['action'] = 'admin_delete';
		$this->Menus->request->params['url']['url'] = 'admin/menus/delete';
		$this->Menus->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Menus->startupProcess();
		$this->Menus->admin_delete(4); // ID of footer
		$this->assertEqual($this->Menus->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Menus->Menu->hasAny(array(
			'Menu.alias' => 'footer',
		));
		$this->assertFalse($hasAny);
	}

	public function endTest($method) {
		$this->Menus->Session->destroy();
		unset($this->Menus);
		ClassRegistry::flush();
	}
}
