<?php
App::uses('LayoutHelper', 'Croogo.View/Helper');
App::uses('SessionComponent', 'Controller/Component');
App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class TheLayoutTestController extends Controller {

	public $name = 'TheTest';

	public $uses = null;

}

class LayoutHelperTest extends CroogoTestCase {

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
		$view = new View(new TheLayoutTestController($request, new CakeResponse()));
		$this->Layout = new LayoutHelper($view);
		$this->Html = new CroogoHtmlHelper($view);
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
		$this->assertContains('var Croogo = {"basePath":"\/","params":{"plugin":null,"controller":"nodes","action":"index","named":[]}};', $f = $this->Layout->js());

		$this->Layout->params['locale'] = 'eng';
		$this->assertContains('var Croogo = {"basePath":"\/eng\/","params":{"plugin":null,"controller":"nodes","action":"index","named":[]}};', $this->Layout->js());
		unset($this->Layout->params['locale']);

		Configure::write('Js.my_var', '123');
		$this->assertContains('var Croogo = {"basePath":"\/","params":{"plugin":null,"controller":"nodes","action":"index","named":[]},"my_var":"123"};', $this->Layout->js());

		Configure::write('Js.my_var2', '456');
		$this->assertContains('var Croogo = {"basePath":"\/","params":{"plugin":null,"controller":"nodes","action":"index","named":[]},"my_var":"123","my_var2":"456"};', $this->Layout->js());
	}

/**
 * testStatus
 */
	public function testStatus() {
		$ok = $this->Html->icon('ok', array('class' => 'green'));
		$remove = $this->Html->icon('remove', array('class' => 'red'));
		$this->assertEqual($this->Layout->status(true), $ok);
		$this->assertEqual($this->Layout->status(1), $ok);
		$this->assertEqual($this->Layout->status(false), $remove);
		$this->assertEqual($this->Layout->status(0), $remove);
	}

/**
 * testIsLoggedIn
 */
	public function testIsLoggedIn() {
		$session = new SessionComponent($this->ComponentCollection);
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
		$session = new SessionComponent($this->ComponentCollection);
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
 * testProcessLink
 */
	public function testProcessLinks() {
		$url = array('controller' => 'users', 'action' => 'edit', 1);
		$expected = array(
			'a' => array(
				'href' => '/users/edit/1',
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
				'data-confirm-message',
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
		$User = ClassRegistry::init('Users.User');
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
		$this->assertContains('icon-ok', $result);

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

/**
 * Test filterElements shortcode detection
 */
	public function testFilterElementWithoutAttributes() {
		$content = 'Lorem [element:element_name] ipsum';
		$View = $this->getMock('View');
		$Layout = new LayoutHelper($View);

		$View
			->expects($this->once())
			->method('element')
			->with(
				$this->equalTo('element_name'),
				$this->equalTo(array()),
				$this->equalTo(array())
			)
			->will($this->returnValue('foobar'));
		$result = $Layout->filterElements($content);

		$expected = 'Lorem foobar ipsum';
		$this->assertEquals($expected, $result);
	}

/**
 * Test filterElements with short syntax
 */
	public function testFilterElementShortSyntax() {
		$content = 'Lorem [e:element_name] ipsum';
		$View = $this->getMock('View');
		$Layout = new LayoutHelper($View);

		$View
			->expects($this->once())
			->method('element')
			->with(
				$this->equalTo('element_name'),
				$this->equalTo(array()),
				$this->equalTo(array())
			)
			->will($this->returnValue('foobar'));
		$result = $Layout->filterElements($content);

		$expected = 'Lorem foobar ipsum';
		$this->assertEquals($expected, $result);
	}

/**
 * Test filterElements with multiple elements
 */
	public function testFilterElementMultipleElements() {
		$content = 'Lorem [element:first] ipsum [element:second] dolor sit.';
		$View = $this->getMock('View');
		$Layout = new LayoutHelper($View);

		$View
			->expects($this->at(0))
			->method('element')
			->with($this->equalTo('first'))
			->will($this->returnValue('LOREM'));
		$View
			->expects($this->at(1))
			->method('element')
			->with($this->equalTo('second'))
			->will($this->returnValue('IPSUM'));
		$result = $Layout->filterElements($content);

		$expected = 'Lorem LOREM ipsum IPSUM dolor sit.';
		$this->assertEquals($expected, $result);
	}

/**
 * Test filterElements and parameter parsing
 */
	public function testFilterElementParseParams() {
		$content = 'Lorem [element:first id=123 cache=var1 nextvar="with quotes" and=\'simple quotes\'] ipsum';
		$View = $this->getMock('View');
		$View->viewVars['block'] = array('title' => 'Hello world');
		$Layout = new LayoutHelper($View);

		$View
			->expects($this->once())
			->method('element')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'id' => 123,
					'nextvar' => 'with quotes',
					'and' => 'simple quotes',
					'block' => array('title' => 'Hello world')
				)),
				$this->equalTo(array('cache' => 'var1'))
			);
		$Layout->filterElements($content);
	}

/**
 * Test filterElements with quoted digits
 */
	public function testFilterElementParamsValueQuotedDigit() {
		$content = 'Lorem [element:issue_list issuesToShow="5"]';
		$View = $this->getMock('View');
		$Layout = new LayoutHelper($View);

		$View
			->expects($this->once())
			->method('element')
			->with(
				$this->equalTo('issue_list'),
				$this->equalTo(array('issuesToShow' => '5')),
				$this->equalTo(array())
			);
		$Layout->filterElements($content);
	}

/**
 * Test filterElements with value containing '=' sign
 */
	public function testFilterElementParamsValueContainsEqual() {
		$content = 'Lorem [element:map plugin="plugandrent" tricky-query="te=st" ]';
		$View = $this->getMock('View');
		$Layout = new LayoutHelper($View);

		$View
			->expects($this->once())
			->method('element')
			->with(
				$this->equalTo('map'),
				$this->equalTo(array('tricky-query' => 'te=st')),
				$this->equalTo(array('plugin' => 'plugandrent'))
			);
		$Layout->filterElements($content);
	}

}
