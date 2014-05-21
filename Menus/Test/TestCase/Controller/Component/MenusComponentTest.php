<?php

App::uses('Controller', 'Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

App::uses('Controller', 'Controller');

class MenusTestController extends Controller {

	public $components = array(
		'Auth',
		'Session',
		'Croogo.Croogo',
		'Blocks.Blocks',
		'Menus.Menus',
	);

	public function beforeFilter() {
		$this->Auth->allow('index');
		parent::beforeFilter();
	}

	public function index() {
	}

}

class MenusComponentTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'plugin.blocks.block',
		'plugin.blocks.region',
		'plugin.menus.menu',
		'plugin.menus.link',
	);

	public function setUp() {
		$this->_paths = App::paths();
		$app = CakePlugin::path('Menus') . 'Test' . DS . 'test_app' . DS;
		App::build(array(
			'Controller' => array(
				$app . 'Controller' . DS,
			),
			'View' => array(
				$app . 'View' . DS,
			),
		));
		$this->generate('MenusTest');
	}

	public function tearDown() {
		App::paths($this->_paths);
		unset($this->controller);
	}

/**
 * test that public Links are displayed
 */
	public function testMenuGenerationForPublic() {
		$vars = $this->testAction('/index', array(
			'return' => 'vars',
		));
		$result = Hash::extract(
			$vars['menus_for_layout'],
			'footer.threaded.{n}.Link[title=Public Link Only]'
		);
		$this->assertNotEmpty($result);
	}

/**
 * test that public Links are not displayed
 */
	public function testMenuGenerationForRegistered() {
		$this->controller->Session->write('Auth.User', array('id' => 3, 'role_id' => 2));
		$vars = $this->testAction('/index', array(
			'return' => 'vars',
		));
		$result = Hash::extract(
			$vars['menus_for_layout'],
			'footer.threaded.{n}.Link[title=Public Link Only]'
		);
		$this->assertEmpty($result);
		$this->controller->Session->delete('Auth');
	}

}
