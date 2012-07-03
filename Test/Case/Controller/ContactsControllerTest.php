<?php
App::uses('ContactsController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');
App::uses('CroogoTestFixture', 'TestSuite');

class TestContactsController extends ContactsController {

	public $name = 'Contacts';

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

class ContactsControllerTest extends CroogoControllerTestCase {

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
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		$request = new CakeRequest();
		$response = new CakeResponse();
		$this->Contacts = new TestContactsController($request, $response);
		$this->Contacts->constructClasses();
		$this->Contacts->Security = $this->getMock('SecurityComponent', null, array($this->Contacts->Components));
		$this->Contacts->request->params['controller'] = 'contacts';
		$this->Contacts->request->params['pass'] = array();
		$this->Contacts->request->params['named'] = array();

		$this->ContactsController = $this->generate('Contacts', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->ContactsController->Auth
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
		unset($this->Contacts);
	}

/**
 * testAdminIndex
 *
 * @return void
 */
	public function testAdminIndex() {
		$this->testAction('/admin/contacts/index');
		$this->assertNotEmpty($this->vars['contacts']);
	}

	public function testAdminAdd() {
		$this->Contacts->request->params['action'] = 'admin_add';
		$this->Contacts->request->params['url']['url'] = 'admin/contacts/add';
		$this->Contacts->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Contacts->request->data = array(
			'Contact' => array(
				'title' => 'New contact',
				'alias' => 'new_contact',
			),
		);
		$this->Contacts->startupProcess();
		$this->Contacts->admin_add();
		$this->assertEqual($this->Contacts->redirectUrl, array('action' => 'index'));

		$newContact = $this->Contacts->Contact->findByAlias('new_contact');
		$this->assertEqual($newContact['Contact']['title'], 'New contact');

		$this->Contacts->testView = true;
		$output = $this->Contacts->render('admin_add');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->ContactsController->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Contact has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->ContactsController
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/contacts/edit/1', array(
			'data' => array(
				'Contact' => array(
					'id' => 1,
					'title' => 'Contact [modified]',
				),
			),
		));
		$result = $this->ContactsController->Contact->findByAlias('contact');
		$this->assertEquals('Contact [modified]', $result['Contact']['title']);
	}

	public function testAdminDelete() {
		$this->Contacts->request->params['action'] = 'admin_delete';
		$this->Contacts->request->params['url']['url'] = 'admin/contacts/delete';
		$this->Contacts->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Contacts->startupProcess();
		$this->Contacts->admin_delete(1);
		$this->assertEqual($this->Contacts->redirectUrl, array('action' => 'index'));

		$hasAny = $this->Contacts->Contact->hasAny(array(
			'Contact.alias' => 'contact',
		));
		$this->assertFalse($hasAny);
	}

/**
 * testView
 */
	public function testView() {
		$Contacts = $this->generate('Contacts', array(
			'methods' => array(
				'_send_email'
			),
		));
		$Contacts->expects($this->once())
			->method('_send_email')
			->will($this->returnValue(true));
		$Contacts->request->params['action'] = 'view';
		$Contacts->request->params['url']['url'] = 'contacts/view/contact';
		$Contacts->request->data = array(
			'Message' => array(
				'name' => 'John Smith',
				'email' => 'john.smith@example.com',
				'title' => 'Hello',
				'body' => 'text here',
			),
		);
		$Contacts->startupProcess();
		$Contacts->view('contact');
		$this->assertEqual($Contacts->viewVars['continue'], true);

		$Contacts->testView = true;
		$output = $Contacts->render('view');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}
}
