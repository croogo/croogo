<?php
App::uses('CroogoHelper', 'Croogo.View/Helper');
App::uses('SessionComponent', 'Controller/Component');
App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeSession', 'Model/Datasource');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('AclHelper', 'Acl.View/Helper');

class TheCroogoTestController extends Controller {

	public $uses = null;

	public $components = array();

}

class CroogoHelperTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
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
		$view = new View(new TheCroogoTestController($request, new CakeResponse()));
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
		$expected = '<ul class="nav nav-stacked"><li><a href="#" class="menu-contents sidebar-item"><i class="icon-white icon-large"></i> <span>Contents</span></a></li></ul>';
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
		$expected = array(
			'a' => array(
				'href' => '/example/example/index/1',
				'class',
			),
			'Title',
			'/a',
		);
		$this->assertTags($result, $expected);

		// test row actions with options
		Configure::write('Admin.rowActions.Test/action', array(
			'Title' => array(
				'plugin:example/controller:example/action:index/:id' => array(
					'options' => array(
						'icon' => 'key',
						'title' => false,
					),
				),
			)
		));
		$result = $this->Croogo->adminRowActions(1);
		$expected = array(
			'a' => array(
				'href' => '/example/example/index/1',
				'class',
			),
			'i' => array(
				'class',
			),
			'/i',
			' Title',
			'/a',
		);
		$this->assertTags($result, $expected);

		// test row actions with no title + icon
		Configure::write('Admin.rowActions.Test/action', array(
			'Title' => array(
				'plugin:example/controller:example/action:edit/:id' => array(
					'title' => false,
					'options' => array(
						'icon' => 'edit',
						'title' => false,
					),
				),
			)
		));
		$result = $this->Croogo->adminRowActions(1);
		$expected = array(
			'a' => array(
				'href' => '/example/example/edit/1',
				'class' => 'edit',
			),
			'i' => array(
				'class' => 'icon-edit icon-large',
			),
			'/i',
			' ',
			'/a',
		);
		$this->assertTags($result, $expected);
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
		$expected = '<li><a href="#test-title" data-toggle="tab">Title</a></li>';
		$this->assertEquals($expected, $result);

		$result = $this->Croogo->adminTabs(true);
		$this->assertContains('test-title', $result);
	}

	public function testAdminBoxes() {
		$this->Croogo->params = array(
			'controller' => 'test',
			'action' => 'action',
		);
		Configure::write('Admin.boxes.Test/action', array(
			'Title' => array(
				'element' => 'blank',
				'options' => array(),
			),
		));

		$result = $this->Croogo->adminBoxes('Title');
		$this->assertContains('class="box"', $result);
	}

	public function testAdminBoxesAlreadyPrinted() {
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

		$this->Croogo->adminBoxes('Title');
		$result = $this->Croogo->adminBoxes('Title');
		$this->assertEquals('', $result);
	}

	public function testAdminBoxesAll() {
		$this->Croogo->params = array(
			'controller' => 'test',
			'action' => 'action',
		);
		Configure::write('Admin.boxes.Test/action', array(
			'Title' => array(
				'element' => 'blank',
				'options' => array(),
			),
			'Content' => array(
				'element' => 'blank',
				'options' => array(),
			),
		));

		$result = $this->Croogo->adminBoxes();
		$this->assertContains('Title', $result);
		$this->assertContains('Content', $result);
	}

	public function testSettingsInputCheckbox() {
		$setting['Setting']['input_type'] = 'checkbox';
		$setting['Setting']['value'] = 0;
		$setting['Setting']['description'] = 'A description';
		$result = $this->Croogo->settingsInput($setting, 'MyLabel', 0);
		$this->assertContains('type="checkbox"',$result);
	}

	public function testSettingsInputCheckboxChecked() {
		$setting['Setting']['input_type'] = 'checkbox';
		$setting['Setting']['value'] = 1;
		$setting['Setting']['description'] = 'A description';
		$result = $this->Croogo->settingsInput($setting, 'MyLabel', 0);
		$this->assertContains('type="checkbox"', $result);
		$this->assertContains('checked="checked"', $result);
	}

	public function testSettingsInputTextbox() {
		$setting['Setting']['input_type'] = '';
		$setting['Setting']['description'] = 'A description';
		$setting['Setting']['value'] = 'Yes';
		$result = $this->Croogo->settingsInput($setting, 'MyLabel', 0);
		$this->assertContains('type="text"', $result);
	}

	public function testSettingsInputTextarea() {
		$setting['Setting']['input_type'] = 'textarea';
		$setting['Setting']['description'] = 'A description';
		$setting['Setting']['value'] = 'Yes';
		$result = $this->Croogo->settingsInput($setting, 'MyLabel', 0);
		$this->assertContains('</textarea>', $result);
	}

/**
 * testAdminRowAction
 */
	public function testAdminRowAction() {
		$url = array('controller' => 'users', 'action' => 'edit', 1);
		$expected = array(
			'a' => array(
				'href' => '/users/edit/1',
				'class' => 'edit',
			),
			'Edit',
			'/a',
		);
		$result = $this->Croogo->adminRowAction('Edit', $url);
		$this->assertTags($result, $expected);

		$options = array('class' => 'test-class');
		$message = 'Are you sure?';
		$expected = array(
			'a' => array(
				'href' => '/users/edit/1',
				'class' => 'test-class edit',
				'onclick' => "return confirm('" . $message . "');",
			),
			'Edit',
			'/a',
		);
		$result = $this->Croogo->adminRowAction('Edit', $url, $options, $message);
		$this->assertTags($result, $expected);
	}

/**
 * testAdminRowActionBulkDelete
 */
	public function testAdminRowActionBulkDelete() {
		$url = '#Node1Id';
		$options = array(
			'rowAction' => 'delete',
		);
		$message = 'Delete this?';
		$expected = array(
			'a' => array(
				'href' => '#Node1Id',
				'data-row-action' => 'delete',
				'data-confirm-message',
			),
			'Delete',
			'/a',
		);
		$result = $this->Croogo->adminRowAction('Delete', $url, $options, $message);
		$this->assertTags($result, $expected);
	}

}
