<?php
App::uses('RolesController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

class TestRolesController extends RolesController {

	public $name = 'Roles';

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

class RolesControllerTest extends CroogoControllerTestCase {

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
		$this->Roles = new TestRolesController($request, $response);
		$this->Roles->constructClasses();
		$this->Roles->Security = $this->getMock('SecurityComponent', null, array($this->Roles->Components));
		$this->Roles->Role->Aro->useDbConfig = $this->Roles->Role->useDbConfig;
		$this->Roles->request->params['controller'] = 'roles';
		$this->Roles->request->params['pass'] = array();
		$this->Roles->request->params['named'] = array();

		$this->RolesController = $this->generate('Roles', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->RolesController->Auth
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
		unset($this->Roles);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/roles/index');
		$this->assertNotEmpty($this->vars['displayFields']);
		$this->assertNotEmpty($this->vars['roles']);
	}

	public function testAdminAdd() {
		$this->Roles->request->params['action'] = 'admin_add';
		$this->Roles->request->params['url']['url'] = 'admin/roles/add';
		$this->Roles->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
		));
		$this->Roles->request->data = array(
			'Role' => array(
				'title' => 'new_role',
				'alias' => 'new_role',
			),
		);
		$this->Roles->startupProcess();
		$this->Roles->admin_add();
		$this->assertEqual($this->Roles->redirectUrl, array('action' => 'index'));

		$newRole = $this->Roles->Role->findByAlias('new_role');
		$this->assertEqual($newRole['Role']['title'], 'new_role');

		$this->Roles->testView = true;
		$output = $this->Roles->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->RolesController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Role has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->RolesController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/roles/edit/1', array(
			'data' => array(
				'Role' => array(
					'id' => 2, // Registered
					'title' => 'Registered [modified]',
				),
			),
		));
		$registered = $this->RolesController->Role->findByAlias('registered');
		$this->assertEquals('Registered [modified]', $registered['Role']['title']);
	}

	public function testAdminDelete() {
		$this->Roles->request->params['action'] = 'admin_delete';
		$this->Roles->request->params['url']['url'] = 'admin/roles/delete';
		$this->Roles->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
		));
		$this->Roles->startupProcess();
		$this->Roles->admin_delete(1); // ID of Admin
		$this->assertEqual($this->Roles->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Roles->Role->hasAny(array(
			'Role.alias' => 'admin',
		));
		$this->assertFalse($hasAny);
	}

}
