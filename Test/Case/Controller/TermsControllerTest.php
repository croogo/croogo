<?php
App::uses('TermsController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

class TestTermsController extends TermsController {

	public $name = 'Terms';

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

class TermsControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
		'block',
		'comment',
		'contact',
		'i18n',
		'language',
		'link',
		'menu',
		'message',
		'meta',
		'node',
		'nodes_taxonomy',
		'region',
		'role',
		'setting',
		'taxonomy',
		'term',
		'type',
		'types_vocabulary',
		'user',
		'vocabulary',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->Terms = new TestTermsController($request, $response);
		$this->Terms->constructClasses();
		$this->Terms->Security = $this->getMock('SecurityComponent', null, array($this->Terms->Components));
		$this->Terms->request->params['named'] = array();
		$this->Terms->request->params['controller'] = 'terms';
		$this->Terms->request->params['pass'] = array();
		$this->Terms->request->params['named'] = array();

		$this->TermsController = $this->generate('Terms', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->TermsController->Auth
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
		unset($this->Terms);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/terms/index/1');
		$this->assertNotEmpty($this->vars['terms']);
		$expected = array(
			'1' => 'Uncategorized',
			'2' => 'Announcements',
		);
		$this->assertEquals($expected, $this->vars['termsTree']);
	}

	public function testAdminAdd() {
		$this->Terms->request->params['action'] = 'admin_add';
		$this->Terms->request->params['url']['url'] = 'admin/terms/add/1';
		$this->Terms->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Terms->request->data = array(
			'Taxonomy' => array(
				'parent_id' => null,
			),
			'Term' => array(
				'title' => 'New Category',
				'slug' => 'new-category',
				'description' => 'category description here',
			),
		);
		$this->Terms->startupProcess();
		$this->Terms->admin_add(1); // ID of categories
		$this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

		$termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
		$termsTreeSlugs = array_keys($termsTree);
		$expected = array(
			'uncategorized',
			'announcements',
			'new-category',
		);
		$this->assertEqual($termsTreeSlugs, $expected);

		$this->Terms->testView = true;
		$output = $this->Terms->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

	public function testAdminAddWithParent() {
		$this->Terms->request->params['action'] = 'admin_add';
		$this->Terms->request->params['url']['url'] = 'admin/terms/add/1';
		$this->Terms->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Terms->request->data = array(
			'Taxonomy' => array(
				'parent_id' => 1, // Uncategorized
			),
			'Term' => array(
				'title' => 'New Category',
				'slug' => 'new-category',
				'description' => 'category description here',
			),
		);
		$this->Terms->startupProcess();
		$this->Terms->admin_add(1); // ID of categories
		$this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

		$termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
		$termsTreeTitles = array_values($termsTree);
		$expected = array(
			'Uncategorized',
			'_New Category',
			'Announcements',
		);
		$this->assertEqual($termsTreeTitles, $expected);
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->TermsController
			->expects($this->once())
			->method('redirect');
		// ID of Uncategorized and Categories
		$this->testAction('/admin/terms/edit/1/1', array(
			'data' => array(
				'Taxonomy' => array(
					'parent_id' => null,
				),
				'Term' => array(
					'title' => 'New Category',
					'slug' => 'new-category',
					'description' => 'category description here',
				),
			),
		));
		$termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
		$expected = array(
			'new-category' => 'New Category',
			'announcements' => 'Announcements',
		);
		$this->assertEquals($expected, $termsTree);
	}

	public function testAdminDelete() {
		$this->Terms->request->params['action'] = 'admin_delete';
		$this->Terms->request->params['url']['url'] = 'admin/terms/delete';
		$this->Terms->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Terms->startupProcess();
		$this->Terms->admin_delete(1, 1); // ID of Uncategorized and Categories
		$this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

		$termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
		$expected = array(
			'announcements' => 'Announcements',
		);
		$this->assertEqual($termsTree, $expected);
	}

	public function testAdminMoveup() {
		$this->Terms->request->params['action'] = 'admin_moveup';
		$this->Terms->request->params['url']['url'] = 'admin/terms/moveup';
		$this->Terms->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Terms->startupProcess();
		$this->Terms->admin_moveup(2, 1); // ID of Announcements and Categories
		$this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

		$termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
		$expected = array(
			'announcements' => 'Announcements',
			'uncategorized' => 'Uncategorized',
		);
		$this->assertEqual($termsTree, $expected);
	}

	public function testAdminMovedown() {
		$this->Terms->request->params['action'] = 'admin_movedown';
		$this->Terms->request->params['url']['url'] = 'admin/terms/movedown';
		$this->Terms->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Terms->startupProcess();
		$this->Terms->admin_movedown(1, 1); // ID of Uncategorized and Categories
		$this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

		$termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
		$expected = array(
			'announcements' => 'Announcements',
			'uncategorized' => 'Uncategorized',
		);
		$this->assertEqual($termsTree, $expected);
	}

}
