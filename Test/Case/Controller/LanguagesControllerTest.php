<?php
App::uses('LanguagesController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

class TestLanguagesController extends LanguagesController {

	public $name = 'Languages';

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

class LanguagesControllerTest extends CroogoControllerTestCase {

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
		$this->Languages = new TestLanguagesController($request, $response);
		$this->Languages->constructClasses();
		$this->Languages->Security = $this->getMock('SecurityComponent', null, array($this->Languages->Components));
		$this->Languages->request->params['controller'] = 'languages';
		$this->Languages->request->params['pass'] = array();
		$this->Languages->request->params['named'] = array();

		$this->LanguagesController = $this->generate('Languages', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->LanguagesController->Auth
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
		unset($this->Languages);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/languages/index');
		$this->assertNotEmpty($this->vars['languages']);
	}

	public function testAdminAdd() {
		$this->Languages->request->params['action'] = 'admin_add';
		$this->Languages->request->params['url']['url'] = 'admin/languages/add';
		$this->Languages->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Languages->request->data = array(
			'Language' => array(
				'title' => 'Bengali',
				'alias' => 'ben',
			),
		);
		$this->Languages->startupProcess();
		$this->Languages->admin_add();
		$this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));

		$ben = $this->Languages->Language->findByAlias('ben');
		$this->assertEqual($ben['Language']['title'], 'Bengali');

		$this->Languages->testView = true;
		$output = $this->Languages->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->LanguagesController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Language has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->LanguagesController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/languages/edit/1', array(
			'data' => array(
				'Language' => array(
					'id' => 1,
					'title' => 'English [modified]',
					'alias' => 'eng',
				),
			),
		));
		$result = $this->LanguagesController->Language->findByAlias('eng');
		$this->assertEquals('English [modified]', $result['Language']['title']);
	}

	public function testAdminDelete() {
		$this->Languages->request->params['action'] = 'admin_delete';
		$this->Languages->request->params['url']['url'] = 'admin/languages/delete';
		$this->Languages->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Languages->startupProcess();
		$this->Languages->admin_delete(1); // ID of English
		$this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Languages->Language->hasAny(array(
			'Language.alias' => 'eng',
		));
		$this->assertFalse($hasAny);
	}

	public function testAdminMove() {
		$this->Languages->request->params['action'] = 'admin_moveup';
		$this->Languages->request->params['url']['url'] = 'admin/languages/moveup';
		$this->Languages->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Languages->startupProcess();

		$this->_testAdminMoveUp();
		$this->_testAdminMoveUpWithSteps();

		$this->_testAdminMoveDown();
		$this->_testAdminMoveDownWithSteps();
	}

	protected function _testAdminMoveUp() {
		// add language
		$this->Languages->Language->save(array(
			'title' => 'Bengali',
			'alias' => 'ben',
		));
		$benId = $this->Languages->Language->id;
		$this->assertEqual($benId, 2, __('Could not add a new language.'));

		// get current list with order
		$list = $this->Languages->Language->find('list', array(
			'order' => 'Language.weight ASC',
		));
		$this->assertEqual($list, array(
			'1' => 'English',
			'2' => 'Bengali',
		));

		// move up
		$this->Languages->admin_moveup($benId);
		$this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));
		$list = $this->Languages->Language->find('list', array(
			'order' => 'Language.weight ASC',
		));
		$this->assertEqual($list, array(
			'2' => 'Bengali',
			'1' => 'English',
		));
	}

	protected function _testAdminMoveUpWithSteps() {
		// add another language
		$this->Languages->Language->id = false;
		$this->Languages->Language->save(array(
			'title' => 'German',
			'alias' => 'deu',
		));
		$deuId = $this->Languages->Language->id;
		$this->assertEqual($deuId, 3, __('Could not add a new language.'));

		// get current list with order
		$list = $this->Languages->Language->find('list', array(
			'order' => 'Language.weight ASC',
		));
		$this->assertEqual($list, array(
			'2' => 'Bengali',
			'1' => 'English',
			'3' => 'German',
		));

		// move up with steps
		$this->Languages->admin_moveup($deuId, 2);
		$this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));
		$list = $this->Languages->Language->find('list', array(
			'order' => 'Language.weight ASC',
		));
		$this->assertEqual($list, array(
			'3' => 'German',
			'2' => 'Bengali',
			'1' => 'English',
		));
	}

	protected function _testAdminMoveDown() {
		$this->Languages->admin_movedown(3);
		$list = $this->Languages->Language->find('list', array(
			'order' => 'Language.weight ASC',
		));
		$this->assertEqual($list, array(
			'2' => 'Bengali',
			'3' => 'German',
			'1' => 'English',
		));
	}

	protected function _testAdminMoveDownWithSteps() {
		$this->Languages->admin_movedown(2, 2);
		$list = $this->Languages->Language->find('list', array(
			'order' => 'Language.weight ASC',
		));
		$this->assertEqual($list, array(
			'3' => 'German',
			'1' => 'English',
			'2' => 'Bengali',
		));
	}

	public function testAdminSelect() {
		$this->Languages->request->params['action'] = 'admin_select';
		$this->Languages->request->params['url']['url'] = 'admin/languages/select';
		$this->Languages->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Languages->startupProcess();

		$this->Languages->admin_select();
		$this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));

		$this->Languages->admin_select(1, 'Node');
		$this->assertEqual($this->Languages->viewVars['id'], 1);
		$this->assertEqual($this->Languages->viewVars['modelAlias'], 'Node');
		$this->assertEqual($this->Languages->viewVars['languages']['0']['Language']['title'], 'English');
		$this->assertEqual($this->Languages->viewVars['languages']['0']['Language']['alias'], 'eng');
	}

}
