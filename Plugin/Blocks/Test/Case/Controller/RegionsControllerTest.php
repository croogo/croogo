<?php
App::uses('RegionsController', 'Blocks.Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

class TestRegionsController extends RegionsController {

	public $name = 'Regions';

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

class RegionsControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'app.aco',
		'app.aro',
		'app.aros_aco',
		'plugin.blocks.block',
		'app.comment',
		'app.contact',
		'app.i18n',
		'app.language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'app.message',
		'plugin.meta.meta',
		'app.node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'app.setting',
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
		App::build(array(
			'View' => array(CakePlugin::path('Blocks') . 'View' . DS)
		), App::APPEND);
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->Regions = new TestRegionsController($request, $response);
		$this->Regions->constructClasses();
		$this->Regions->Security = $this->getMock('SecurityComponent', null, array($this->Regions->Components));
		$this->Regions->request->params['controller'] = 'regions';
		$this->Regions->request->params['pass'] = array();
		$this->Regions->request->params['named'] = array();

		$this->RegionsController = $this->generate('Regions', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->RegionsController->Auth
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
		unset($this->Regions);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/blocks/regions/index');
		$this->assertNotEmpty($this->vars['displayFields']);
		$this->assertNotEmpty($this->vars['regions']);
	}

	public function testAdminAdd() {
		$this->Regions->request->params['action'] = 'admin_add';
		$this->Regions->request->params['url']['url'] = 'admin/blocks/regions/add';
		$this->Regions->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Regions->request->data = array(
			'Region' => array(
				'title' => 'new_region',
				'alias' => 'new_region',
				'description' => 'A new region',
			),
		);
		$this->Regions->startupProcess();
		$this->Regions->admin_add();
		$this->assertEqual($this->Regions->redirectUrl, array('action' => 'index'));

		$newRegion = $this->Regions->Region->findByAlias('new_region');
		$this->assertEqual($newRegion['Region']['title'], 'new_region');

		$this->Regions->testView = true;
		$output = $this->Regions->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->RegionsController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Region has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->RegionsController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/blocks/regions/edit/1', array(
			'data' => array(
				'Region' => array(
					'id' => 4, // right
					'title' => 'right_modified',
				),
			),
		));
		$right = $this->RegionsController->Region->findByAlias('right');
		$this->assertEquals('right_modified', $right['Region']['title']);
	}

	public function testAdminDelete() {
		$this->Regions->request->params['action'] = 'admin_delete';
		$this->Regions->request->params['url']['url'] = 'admin/blocks/regions/delete';
		$this->Regions->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Regions->startupProcess();
		$this->Regions->admin_delete(4); // ID of right
		$this->assertEqual($this->Regions->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Regions->Region->hasAny(array(
			'Region.alias' => 'right',
		));
		$this->assertFalse($hasAny);
	}

}
