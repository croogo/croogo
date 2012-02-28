<?php
App::uses('LanguagesController', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

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

	public function _stop($status = 0) {
		$this->stopped = $status;
	}

	public function __securityError() {

	}
}

class LanguagesControllerTest extends CroogoTestCase {

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

	public function startTest() {
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->Languages = new TestLanguagesController($request, $response);
		$this->Languages->constructClasses();
		$this->Languages->request->params['controller'] = 'languages';
		$this->Languages->request->params['pass'] = array();
		$this->Languages->request->params['named'] = array();
	}

	public function testAdminIndex() {
		$this->Languages->request->params['action'] = 'admin_index';
		$this->Languages->request->params['url']['url'] = 'admin/languages';
		$this->Languages->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Languages->startupProcess();
		$this->Languages->admin_index();

		$this->Languages->testView = true;
		$output = $this->Languages->render('admin_index');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
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

	public function testAdminEdit() {
		$this->Languages->request->params['action'] = 'admin_edit';
		$this->Languages->request->params['url']['url'] = 'admin/languages/edit';
		$this->Languages->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Languages->request->data = array(
			'Language' => array(
				'id' => 1,
				'title' => 'English [modified]',
				'alias' => 'eng',
			),
		);
		$this->Languages->startupProcess();
		$this->Languages->admin_edit();
		$this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));

		$eng = $this->Languages->Language->findByAlias('eng');
		$this->assertEqual($eng['Language']['title'], 'English [modified]');

		$this->Languages->testView = true;
		$output = $this->Languages->render('admin_edit');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
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

		$this->__testAdminMoveUp();
		$this->__testAdminMoveUpWithSteps();

		$this->__testAdminMoveDown();
		$this->__testAdminMoveDownWithSteps();
	}

	private function __testAdminMoveUp() {
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

	private function __testAdminMoveUpWithSteps() {
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

	private function __testAdminMoveDown() {
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

	private function __testAdminMoveDownWithSteps() {
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

	public function endTest() {
		$this->Languages->Session->destroy();
		unset($this->Languages);
		ClassRegistry::flush();
	}
}
