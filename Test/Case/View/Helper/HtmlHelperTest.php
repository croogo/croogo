<?php
App::uses('LayoutHelper', 'View/Helper');
App::uses('SessionComponent', 'Controller/Component');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class TheLayoutTestController extends Controller {

	public $name = 'TheTest';

	public $uses = null;

}

class HtmlHelperTest extends CroogoTestCase {

	public $fixtures = array(
		'user', 'role', 'setting',
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
		$view =& new View(new TheLayoutTestController($request, new CakeResponse()));
		$this->Layout =& new LayoutHelper($view);
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
 * testJs
 */
	public function testJs() {
		$this->assertContains('var Croogo = {"basePath":"\/","params":{"controller":"nodes","action":"index","named":[]}};', $f = $this->Layout->js());

		$this->Layout->params['locale'] = 'eng';
		$this->assertContains('var Croogo = {"basePath":"\/eng\/","params":{"controller":"nodes","action":"index","named":[]}};', $this->Layout->js());
		unset($this->Layout->params['locale']);

		Configure::write('Js.my_var', '123');
		$this->assertContains('var Croogo = {"basePath":"\/","params":{"controller":"nodes","action":"index","named":[]},"my_var":"123"};', $this->Layout->js());

		Configure::write('Js.my_var2', '456');
		$this->assertContains('var Croogo = {"basePath":"\/","params":{"controller":"nodes","action":"index","named":[]},"my_var":"123","my_var2":"456"};', $this->Layout->js());
	}

/**
 * testStatus
 */
	public function testStatus() {
		$this->assertEqual($this->Layout->status(true), $this->Layout->Html->image('/img/icons/tick.png'));
		$this->assertEqual($this->Layout->status(1), $this->Layout->Html->image('/img/icons/tick.png'));
		$this->assertEqual($this->Layout->status(false), $this->Layout->Html->image('/img/icons/cross.png'));
		$this->assertEqual($this->Layout->status(0), $this->Layout->Html->image('/img/icons/cross.png'));
	}

/**
 * testIsLoggedIn
 */
	public function testIsLoggedIn() {
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

/**
 * testGetRoleId
 */
	public function testGetRoleId() {
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

/**
 * testRegionIsEmpty
 */
	public function testRegionIsEmpty() {
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

/**
 * testLinkStringToArray
 */
	public function testLinkStringToArray() {
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

/**
 * testProcessLink
 */
	public function testProcessLinks() {
		$url = array('controller' => 'users', 'action' => 'edit', 1);
		$expected = array(
			'a' => array(
				'href' => '/users/edit/1',
				'onclick' => 'Admin.processLink(this); return false;',
				),
			'Edit',
			'/a',
			);
		$result = $this->Layout->processLink('Edit', $url);
		$this->assertTags($result, $expected);

		$options = array('class' => 'test-class');
		$message = 'Are you sure';
		$expected = array(
			'a' => array(
				'href' => '/users/edit/1',
				'class' => 'test-class',
				'onclick' => 'if (confirm(&#039;Are you sure&#039;)) { Admin.processLink(this); } return false;',
				),
			'Edit',
			'/a',
			);
		$result = $this->Layout->processLink('Edit', $url, $options, $message);
		$this->assertTags($result, $expected);
	}

/**
 * testDisplayFields
 */
	public function testDisplayFields() {
		$User = ClassRegistry::init('User');
		$rows = $User->find('all');

		$expected = '1';
		$options = array(
			'type' => null,
			'url' => null,
			'options' => array(),
			);
		$result = $this->Layout->displayField($rows[0], 'User', 'id', $options);
		$this->assertEqual($expected, $result);

		$expected = 'admin';
		$options = array(
			'type' => null,
			'url' => null,
			'options' => array(),
			);
		$result = $this->Layout->displayField($rows[0], 'User', 'username', $options);
		$this->assertEqual($expected, $result);

		$options = array(
			'type' => 'boolean',
			'url' => null,
			'options' => array(),
			);
		$result = $this->Layout->displayField($rows[0], 'User', 'status', $options);
		$this->assertContains('tick.png', $result);

		$expected = '<a href="/users/view/1">admin</a>';
		$options = array(
			'type' => null,
			'url' => array(
				'plugin' => false,
				'controller' => 'users',
				'action' => 'view',
				'pass' => 'id'
				),
			'options' => array(),
			);
		$result = $this->Layout->displayField($rows[0], 'User', 'username', $options);
		$this->assertEqual($expected, $result);

		$expected = '<a href="/admin/roles/view/1">Admin</a>';
		$options = array(
			'type' => null,
			'url' => array(
				'admin' => true,
				'plugin' => false,
				'controller' => 'roles',
				'action' => 'view',
				'pass' => 'id'
				),
			'options' => array(),
			);
		$result = $this->Layout->displayField($rows[0], 'Role', 'title', $options);
		$this->assertEqual($expected, $result);

		$expected = '<a href="/users/view/1/admin">admin</a>';
		$options = array(
			'type' => null,
			'url' => array(
				'plugin' => false,
				'controller' => 'users',
				'action' => 'view',
				'pass' => array('id', 'username'),
				),
			'options' => array(),
			);
		$result = $this->Layout->displayField($rows[0], 'User', 'username', $options);
		$this->assertEqual($expected, $result);

		$expected = '<a href="/users/view/id:1/username:admin">admin</a>';
		$options = array(
			'type' => null,
			'url' => array(
				'plugin' => false,
				'controller' => 'users',
				'action' => 'view',
				'named' => array('id', 'username'),
				),
			'options' => array(),
			);
		$result = $this->Layout->displayField($rows[0], 'User', 'username', $options);
		$this->assertEqual($expected, $result);

		$expected = '<a href="/users/view/id:1/username:admin" class="view">admin</a>';
		$options = array(
			'type' => null,
			'url' => array(
				'plugin' => false,
				'controller' => 'users',
				'action' => 'view',
				'named' => array('id', 'username'),
				),
			'options' => array('class' => 'view'),
			);
		$result = $this->Layout->displayField($rows[0], 'User', 'username', $options);
		$this->assertEqual($expected, $result);
	}

}
