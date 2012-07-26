<?php
App::uses('MenusHelper', 'View/Helper');
App::uses('SessionComponent', 'Controller/Component');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class TheMenuTestController extends Controller {

	public $name = 'TheTest';

	public $uses = null;

}

class MenusHelperTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.user',
		'plugin.users.role',
		'plugin.settings.setting',
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
		$view = new View(new TheMenuTestController($request, new CakeResponse()));
		$this->Menus = new MenusHelper($view);
		$this->_appEncoding = Configure::read('App.encoding');
		$this->_asset = Configure::read('Asset');
		$this->_debug = Configure::read('debug');
	}

/**
 * tearDown
 */
	public function tearDown() {
		Configure::write('App.encoding', $this->_appEncoding);
		Configure::write('Asset', $this->_asset);
		Configure::write('debug', $this->_debug);
		ClassRegistry::flush();
		unset($this->Layout);
	}

/**
 * testLinkStringToArray
 */
	public function testLinkStringToArray() {
		$this->assertEqual($this->Menus->linkStringToArray('controller:nodes/action:index'), array(
			'plugin' => null,
			'controller' => 'nodes',
			'action' => 'index',
		));
		$this->assertEqual($this->Menus->linkStringToArray('controller:nodes/action:index/pass/pass2'), array(
			'plugin' => null,
			'controller' => 'nodes',
			'action' => 'index',
			'pass',
			'pass2',
		));
		$this->assertEqual($this->Menus->linkStringToArray('controller:nodes/action:index/param:value'), array(
			'plugin' => null,
			'controller' => 'nodes',
			'action' => 'index',
			'param' => 'value',
		));
		$this->assertEqual($this->Menus->linkStringToArray('controller:nodes/action:index/with-slash/'), array(
			'plugin' => null,
			'controller' => 'nodes',
			'action' => 'index',
			'with-slash',
		));
	}

}
