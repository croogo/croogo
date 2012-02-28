<?php
App::import('Helper', array(
	'Html',
	'Form',
	'Session',
	'Js',
	'Layout',
));
App::uses('SessionComponent', 'Controller/Component');
App::uses('Controller', 'Controller');

class TheLayoutTestController extends Controller {
	var $name = 'TheTest';
	var $uses = null;
}

class HtmlHelperTest extends CakeTestCase {

	function startTest() {
		$request = new CakeRequest('nodes/index');
		$request->params = array(
			'controller' => 'nodes',
			'action' => 'index',
			'named' => array(),
		);
		$view =& new View(new TheLayoutTestController($request, new CakeResponse()));
		$this->Layout =& new LayoutHelper($view);
		$this->_appEncoding = Configure::read('App.encoding');
		$this->_asset = Configure::read('Asset');
		$this->_debug = Configure::read('debug');
	}

	function testJs() {
		$this->assertContains('var Croogo = {"basePath":"\/","params":{"controller":"nodes","action":"index","named":[]}};', $f = $this->Layout->js());
	
		$this->Layout->params['locale'] = 'eng';
		$this->assertContains('var Croogo = {"basePath":"\/eng\/","params":{"controller":"nodes","action":"index","named":[]}};', $this->Layout->js());
		unset($this->Layout->params['locale']);

		Configure::write('Js.my_var', '123');
		$this->assertContains('var Croogo = {"basePath":"\/","params":{"controller":"nodes","action":"index","named":[]},"my_var":"123"};', $this->Layout->js());
		
		Configure::write('Js.my_var2', '456');
		$this->assertContains('var Croogo = {"basePath":"\/","params":{"controller":"nodes","action":"index","named":[]},"my_var":"123","my_var2":"456"};', $this->Layout->js());
	}

	function testStatus() {
		$this->assertEqual($this->Layout->status(true), $this->Layout->Html->image('/img/icons/tick.png'));
		$this->assertEqual($this->Layout->status(1), $this->Layout->Html->image('/img/icons/tick.png'));
		$this->assertEqual($this->Layout->status(false), $this->Layout->Html->image('/img/icons/cross.png'));
		$this->assertEqual($this->Layout->status(0), $this->Layout->Html->image('/img/icons/cross.png'));
	}

	function setUp() {
		parent::setUp();
		$this->ComponentCollection = new ComponentCollection();
	}

	function testIsLoggedIn() {
		$session =& new SessionComponent($this->ComponentCollection);
		$session->delete('Auth');
		$this->assertFalse($this->Layout->isLoggedIn());

		$session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->assertTrue($this->Layout->isLoggedIn());
		$session->delete('Auth');
	}

	function testGetRoleId() {
		$session =& new SessionComponent($this->ComponentCollection);
		$session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
			'role_id' => 1,
		));
		$this->assertEqual($this->Layout->getRoleId(), 1);

		$session->delete('Auth');
		$this->assertEqual($this->Layout->getRoleId(), 3);
	}

	function testRegionIsEmpty() {
		$this->assertTrue($this->Layout->regionIsEmpty('right'));

		$this->Layout->_View->viewVars['blocks_for_layout'] = array(
			'right' => array(
				'0' => array('block here'),
				'1' => array('block here'),
				'2' => array('block here'),
			),
		);
		$this->assertFalse($this->Layout->regionIsEmpty('right'));
	}

	function testLinkStringToArray() {
		$this->assertEqual($this->Layout->linkStringToArray('controller:nodes/action:index'), array(
			'plugin' => null,
			'controller' => 'nodes',
			'action' => 'index',
		));
		$this->assertEqual($this->Layout->linkStringToArray('controller:nodes/action:index/pass/pass2'), array(
			'plugin' => null,
			'controller' => 'nodes',
			'action' => 'index',
			'pass',
			'pass2',
		));
		$this->assertEqual($this->Layout->linkStringToArray('controller:nodes/action:index/param:value'), array(
			'plugin' => null,
			'controller' => 'nodes',
			'action' => 'index',
			'param' => 'value',
		));
		$this->assertEqual($this->Layout->linkStringToArray('controller:nodes/action:index/with-slash/'), array(
			'plugin' => null,
			'controller' => 'nodes',
			'action' => 'index',
			'with-slash',
		));
	}

	function endTest() {
		Configure::write('App.encoding', $this->_appEncoding);
		Configure::write('Asset', $this->_asset);
		Configure::write('debug', $this->_debug);
		ClassRegistry::flush();
		unset($this->Layout);
	}

}
