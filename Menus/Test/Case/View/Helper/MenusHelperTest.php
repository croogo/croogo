<?php

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

		$expected = array(
			'plugin' => 'contacts',
			'controller' => 'contacts',
			'action' => 'view',
			'contact',
		);
		$string = 'plugin:contacts/controller:contacts/action:view/contact';
		$this->assertEqual($expected, $this->Menus->linkStringToArray($string));

		$string = '/plugin:contacts/controller:contacts/action:view/contact';
		$this->assertEqual($expected, $this->Menus->linkStringToArray($string));
	}

/**
 * testUrlToLinkString
 */
	public function testUrlToLinkString() {
		$url = array(
			'controller' => 'contacts',
			'action' => 'view',
			'contact',
			'plugin' => 'contacts',
		);
		$expected = 'plugin:contacts/controller:contacts/action:view/contact';
		$this->assertEquals($expected, $this->Menus->urlToLinkString($url));

		$url = array(
			'plugin' => 'contacts',
			'controller' => 'contacts',
			'action' => 'view',
			'contact',
		);
		$expected = 'plugin:contacts/controller:contacts/action:view/contact';
		$this->assertEquals($expected, $this->Menus->urlToLinkString($url));

		$url = array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'type' => 'blog',
			'hello'
		);
		$expected = 'plugin:nodes/controller:nodes/action:view/type:blog/hello';
		$this->assertEquals($expected, $this->Menus->urlToLinkString($url));

		$url = array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'live',
			'long',
			'and',
			'prosper',
		);
		$expected = 'plugin:nodes/controller:nodes/action:view/live/long/and/prosper';
		$this->assertEquals($expected, $this->Menus->urlToLinkString($url));

		$url = array(
			'controller' => 'nodes',
			'action' => 'view',
			'live',
			'long',
			'and',
			'prosper',
		);
		$expected = 'controller:nodes/action:view/live/long/and/prosper';
		$this->assertEquals($expected, $this->Menus->urlToLinkString($url));

		$url = array(
			'admin' => true,
			'controller' => 'nodes',
			'action' => 'edit',
			1,
			'type' => 'blog',
		);
		$expected = 'admin/controller:nodes/action:edit/1/type:blog';
		$this->assertEquals($expected, $this->Menus->urlToLinkString($url));

		$url = array();
		$this->assertEquals('', $this->Menus->urlToLinkString($url));

		$url = array('some' => 'random', 1, 2, 'array' => 'must', 'work');
		$expected = 'some:random/1/2/array:must/work';
		$this->assertEquals($expected, $this->Menus->urlToLinkString($url));
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
