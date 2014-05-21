<?php

namespace Croogo\Menus\Test\TestCase\View\Helper;
App::uses('MenusHelper', 'Menus.View/Helper');
App::uses('SessionComponent', 'Controller/Component');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

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

		$request = $this->getMock('CakeRequest');
		$response = $this->getMock('CakeResponse');
		$this->View = new View(new TheMenuTestController($request, $response));
		$this->Menus = new MenusHelper($this->View);
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
 * Test [menu] shortcode
 */
	public function testMenuShortcode() {
		$content = '[menu:blogroll]';
		$this->View->viewVars['menus_for_layout']['blogroll'] = array(
			'Menu' => array(
				'id' => 6,
				'title' => 'Blogroll',
				'alias' => 'blogroll',
			),
			'threaded' => array(),
		);
		Croogo::dispatchEvent('Helper.Layout.beforeFilter', $this->View, array('content' => &$content));
		$this->assertContains('menu-6', $content);
		$this->assertContains('class="menu"', $content);
	}

}
