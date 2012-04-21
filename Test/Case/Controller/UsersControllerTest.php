<?php
App::uses('UsersController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

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

/**
 * UsersController Test
 */
class UsersControllerTest extends CroogoControllerTestCase {

/**
 * fixtures
 *
 * @var array
 */
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
		$this->Users = new TestUsersController($request, $response);
		$this->Users->constructClasses();
		$this->Users->User->Aro->useDbConfig = $this->Users->User->useDbConfig;
		$this->Users->request->params['controller'] = 'users';
		$this->Users->request->params['pass'] = array();
		$this->Users->request->params['named'] = array();

		$this->UsersController = $this->generate('Users', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->UsersController->Auth
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
		unset($this->Users);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	function testAdminIndex() {
		$this->testAction('/admin/users/index');
		$this->assertNotEmpty($this->vars['displayFields']);
		$this->assertNotEmpty($this->vars['users']);
	}

/**
 * testAdd
 *
 * @return void
 */
	public function testAdd() {
		$_SERVER['SERVER_NAME'] = 'croogo.dev';
		$this->Users->request->params['action'] = 'add';
		$this->Users->request->params['url']['url'] = 'users/add';
		$this->Users->request->data = array(
			'User' => array(
				'username' => 'new_user',
				'password' => '',
				'email' => 'new_user@croogo.dev',
				'name' => 'New User',
				'website' => '',
				'role_id' => 3,
			),
		);
		$this->Users->startupProcess();
		$User = $this->Users->User;

		$this->Users->add();
		$errors = print_r($User->validationErrors, true);
		$this->assertContains('at least 6 characters', $errors);

		$this->Users->request->data['User']['username'] = 'admin';
		$this->Users->request->data['User']['password'] = 'yvonne';
		$this->Users->request->data['User']['verify_password'] = 'strahovski';
		$this->Users->request->data['User']['email'] = '123456';
		$this->Users->add();
		$errors = print_r($User->validationErrors, true);
		$this->assertContains('do not match', $errors);
		$this->assertContains('valid email', $errors);
		$this->assertContains('been taken', $errors);
	}

/**
 * testAdminAdd
 *
 * @return void
 */
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

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->UsersController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The User has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->UsersController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/users/edit/1', array(
			'data' => array(
				'User' => array(
					'id' => 1, // admin
					'name' => 'Administrator [modified]',
					'role_id' => 1,
				),
			),
		));
		$this->assertNotEmpty($this->vars['editFields']);
		$expected = 'Administrator [modified]';
		$this->assertEquals($expected, $this->controller->request->data['User']['name']);
		$result = $this->UsersController->User->findByUsername('admin');
		$this->assertEquals($expected, $result['User']['name']);
	}

/**
 * testAdminDelete
 *
 * @return void
 */
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

}
