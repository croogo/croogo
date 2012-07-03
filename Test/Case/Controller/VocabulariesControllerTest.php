<?php
App::uses('VocabulariesController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

class TestVocabulariesController extends VocabulariesController {

	public $name = 'Vocabularies';

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

/**
 * VocabulariesController Test
 */
class VocabulariesControllerTest extends CroogoControllerTestCase {

/**
 * fixtures
 */
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
		$this->Vocabularies = new TestVocabulariesController($request, $response);
		$this->Vocabularies->constructClasses();
		$this->Vocabularies->Security = $this->getMock('SecurityComponent', null, array($this->Vocabularies->Components));
		$this->Vocabularies->request->params['controller'] = 'vocabularies';
		$this->Vocabularies->request->params['pass'] = array();
		$this->Vocabularies->request->params['named'] = array();

		$this->VocabulariesController = $this->generate('Vocabularies', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->VocabulariesController->Auth
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
		unset($this->Vocabularies);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/vocabularies/index');
		$this->assertNotEmpty($this->vars['vocabularies']);
	}

	public function testAdminAdd() {
		$this->Vocabularies->request->params['action'] = 'admin_add';
		$this->Vocabularies->request->params['url']['url'] = 'admin/vocabularies/add';
		$this->Vocabularies->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Vocabularies->request->data = array(
			'Vocabulary' => array(
				'title' => 'New Vocabulary',
				'alias' => 'new_vocabulary',
			),
		);
		$this->Vocabularies->startupProcess();
		$this->Vocabularies->admin_add();
		$this->assertEqual($this->Vocabularies->redirectUrl, array('action' => 'index'));

		$newVocabulary = $this->Vocabularies->Vocabulary->findByAlias('new_vocabulary');
		$this->assertEqual($newVocabulary['Vocabulary']['title'], 'New Vocabulary');

		$this->Vocabularies->testView = true;
		$output = $this->Vocabularies->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->VocabulariesController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Vocabulary has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->VocabulariesController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/vocabularies/edit/1', array(
			'data' => array(
				'Vocabulary' => array(
					'id' => 1, // categories
					'title' => 'Categories [modified]',
				),
			),
		));
		$categories = $this->VocabulariesController->Vocabulary->findByAlias('categories');
		$this->assertEquals('Categories [modified]', $categories['Vocabulary']['title']);
	}

	public function testAdminDelete() {
		$this->Vocabularies->request->params['action'] = 'admin_delete';
		$this->Vocabularies->request->params['url']['url'] = 'admin/vocabularies/delete';
		$this->Vocabularies->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Vocabularies->startupProcess();
		$this->Vocabularies->admin_delete(1); // ID of categories
		$this->assertEqual($this->Vocabularies->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Vocabularies->Vocabulary->hasAny(array(
			'Vocabulary.alias' => 'categories',
		));
		$this->assertFalse($hasAny);
	}

	public function testAdminMoveup() {
		$this->Vocabularies->request->params['action'] = 'admin_index';
		$this->Vocabularies->request->params['url']['url'] = 'admin/vocabularies/moveup';
		$this->Vocabularies->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Vocabularies->startupProcess();
		$this->Vocabularies->admin_moveup(2); // ID of tags
		$this->assertEqual($this->Vocabularies->redirectUrl, array('action' => 'index'));

		$vocabularies = $this->Vocabularies->Vocabulary->find('list', array(
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Vocabulary.weight ASC',
		));
		$expected = array(
			'2' => 'tags',
			'1' => 'categories',
		);
		$this->assertEqual($vocabularies, $expected);
	}

	public function testAdminMovedown() {
		$this->Vocabularies->request->params['action'] = 'admin_index';
		$this->Vocabularies->request->params['url']['url'] = 'admin/vocabularies/moveup';
		$this->Vocabularies->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Vocabularies->startupProcess();
		$this->Vocabularies->admin_movedown(1); // ID of categories
		$this->assertEqual($this->Vocabularies->redirectUrl, array('action' => 'index'));

		$vocabularies = $this->Vocabularies->Vocabulary->find('list', array(
			'fields' => array(
				'id',
				'alias',
			),
			'order' => 'Vocabulary.weight ASC',
		));
		$expected = array(
			'2' => 'tags',
			'1' => 'categories',
		);
		$this->assertEqual($vocabularies, $expected);
	}

}
