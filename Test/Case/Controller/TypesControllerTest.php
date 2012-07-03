<?php
App::uses('TypesController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

class TestTypesController extends TypesController {

	public $name = 'Types';

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

class TypesControllerTest extends CroogoControllerTestCase {

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
		$this->Types = new TestTypesController($request, $response);
		$this->Types->constructClasses();
		$this->Types->Security = $this->getMock('SecurityComponent', null, array($this->Types->Components));
		$this->Types->request->params['controller'] = 'types';
		$this->Types->request->params['pass'] = array();
		$this->Types->request->params['named'] = array();

		$this->TypesController = $this->generate('Types', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->TypesController->Auth
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
		unset($this->Types);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/types/index');
		$this->assertNotEmpty($this->vars['displayFields']);
		$this->assertNotEmpty($this->vars['types']);
	}

	public function testAdminAdd() {
		$this->Types->request->params['action'] = 'admin_add';
		$this->Types->request->params['url']['url'] = 'admin/types/add';
		$this->Types->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Types->request->data = array(
			'Type' => array(
				'title' => 'New Type',
				'alias' => 'new_type',
				'description' => 'A new type',
			),
		);
		$this->Types->startupProcess();
		$this->Types->admin_add();
		$this->assertEqual($this->Types->redirectUrl, array('action' => 'index'));

		$newType = $this->Types->Type->findByAlias('new_type');
		$this->assertEqual($newType['Type']['title'], 'New Type');

		$this->Types->testView = true;
		$output = $this->Types->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->TypesController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Type has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->TypesController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/types/edit/1', array(
			'data' => array(
				'Type' => array(
					'id' => 1, // page
					'description' => '[modified]',
				),
			),
		));
		$page = $this->TypesController->Type->findByAlias('page');
		$this->assertEquals('[modified]', $page['Type']['description']);
	}

	public function testAdminDelete() {
		$this->Types->request->params['action'] = 'admin_delete';
		$this->Types->request->params['url']['url'] = 'admin/types/delete';
		$this->Types->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Types->startupProcess();
		$this->Types->admin_delete(1); // ID of page
		$this->assertEqual($this->Types->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Types->Type->hasAny(array(
			'Type.alias' => 'page',
		));
		$this->assertFalse($hasAny);
	}

}
