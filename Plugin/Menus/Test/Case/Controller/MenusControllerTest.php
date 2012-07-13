<?php
App::uses('MenusController', 'Menus.Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

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

	protected function _stop($status = 0) {
		$this->stopped = $status;
	}

	public function securityError($type) {
	}

}

class MenusControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
		'plugin.blocks.block',
		'comment',
		'plugin.contacts.contact',
		'i18n',
		'language',
		'plugin.contacts.message',
		'plugin.contents.node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'setting',
		'plugin.menus.menu',
		'plugin.menus.link',
		'plugin.meta.meta',
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
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->Menus = new TestMenusController($request, $response);
		$this->Menus->constructClasses();
		$this->Menus->Security = $this->getMock('SecurityComponent', null, array($this->Menus->Components));
		$this->Menus->Components->unload('Acl');
		$this->Menus->Components->unload('Auth');
		$this->Menus->plugin = 'Menus';
		$this->Menus->request->params['plugin'] = 'menus';
		$this->Menus->request->params['controller'] = 'menus';
		$this->Menus->request->params['pass'] = array();
		$this->Menus->request->params['named'] = array();

		$this->MenusController = $this->generate('Menus', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->MenusController->Auth
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
		unset($this->Menus);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/menus/menus/index');
		$this->assertNotEmpty($this->vars['menus']);
	}

	public function testAdminAdd() {
		$this->Menus->request->params['action'] = 'admin_add';
		$this->Menus->request->params['url']['url'] = 'admin/menus/menus/add';
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

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->MenusController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Menu has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->MenusController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/menus/menus/edit/1', array(
			'data' => array(
				'Menu' => array(
					'id' => 3, // main
					'title' => 'Main Menu [modified]',
				),
			),
		));
		$result = $this->MenusController->Menu->findByAlias('main');
		$this->assertEquals('Main Menu [modified]', $result['Menu']['title']);
	}

	public function testAdminDelete() {
		$this->Menus->request->params['action'] = 'admin_delete';
		$this->Menus->request->params['url']['url'] = 'admin/menus/menus/delete';
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

}
