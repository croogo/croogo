<?php
App::uses('CroogoHelper', 'View/Helper');
App::uses('SessionComponent', 'Controller/Component');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');
App::uses('AclHelper', 'Acl.View/Helper');

class TheCroogoTestController extends Controller {

	public $uses = null;

	public $components = array();

}

class CroogoHelperTest extends CroogoTestCase {

	public $fixtures = array(
		'app.aco',
		'app.aro',
		'app.aros_aco',
		'plugin.settings.setting',
		'plugin.users.role',
	);

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->ComponentCollection = new ComponentCollection();

		$request = new CakeRequest('nodes/index');
		$request->params = array(
			'controller' => 'nodes',
			'action' => 'index',
			'named' => array(),
		);
		$view = new View(new TheLayoutTestController($request, new CakeResponse()));
		$this->Croogo = new CroogoHelper($view);
		$aclHelper = Configure::read('Site.acl_plugin') . 'Helper';
		$this->Croogo->Acl = $this->getMock(
			$aclHelper,
			array('linkIsAllowedByRoleId'),
			array($view)
		);
		$this->Croogo->Acl
			->expects($this->any())
			->method('linkIsAllowedByRoleId')
			->will($this->returnValue(true));
		$this->menus = CroogoNav::items();
		CroogoNav::clear();
	}

/**
 * tearDown
 */
	public function tearDown() {
		ClassRegistry::flush();
		CroogoNav::items($this->menus);
		unset($this->Croogo);
	}

/**
 * testAdminMenus
 */
	public function testAdminMenus() {
		CakeSession::write('Auth.User', array('id' => 1, 'role_id' => 1));
		CroogoNav::add('contents', array(
			'title' => 'Contents',
			'url' => '#',
			)
		);
		$items = CroogoNav::items();
		$expected = '<ul class="sf-menu"><li><a href="#" class="menu-contents">Contents</a></li></ul>';
		$result = $this->Croogo->adminMenus(CroogoNav::items());
		$this->assertEquals($expected, $result);
	}

/**
 * testAdminRowActions
 */
	public function testAdminRowActions() {
		$this->Croogo->params = array(
			'controller' => 'test',
			'action' => 'action',
			);
		Configure::write('Admin.rowActions.Test/action', array(
			'Title' => 'plugin:example/controller:example/action:index/:id',
		));
		$result = $this->Croogo->adminRowActions(1);
		$expected = '<a href="/example/example/index/1">Title</a>';
		$this->assertEquals($expected, $result);
	}

/**
 * testAdminTabs
 */
	public function testAdminTabs() {
		$this->Croogo->params = array(
			'controller' => 'test',
			'action' => 'action',
			);
		Configure::write('Admin.tabs.Test/action', array(
			'Title' => array(
				'element' => 'blank',
				'options' => array(),
			),
		));
		$result = $this->Croogo->adminTabs();
		$expected = '<li><a href="#test-title">Title</a></li>';
		$this->assertEquals($expected, $result);

		$result = $this->Croogo->adminTabs(true);
		$this->assertContains('test-title', $result);
	}

}
