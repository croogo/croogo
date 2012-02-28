<?php
App::import('Controller', 'Contacts');

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

	public function _stop($status = 0) {
		$this->stopped = $status;
	}

	public function __securityError() {

	}
}

App::uses('CroogoTestCase', 'TestSuite');

class ContactsControllerTest extends CroogoTestCase {

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
		$this->Contacts = new TestContactsController($request, $response);
		$this->Contacts->constructClasses();
		$this->Contacts->request->params['controller'] = 'contacts';
		$this->Contacts->request->params['pass'] = array();
		$this->Contacts->request->params['named'] = array();
	}

	public function testAdminIndex() {
		$this->Contacts->request->params['action'] = 'admin_index';
		$this->Contacts->request->params['url'] = 'admin/contacts';
		$this->Contacts->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Contacts->startupProcess();
		$this->Contacts->admin_index();

		$this->Contacts->testView = true;
		$output = $this->Contacts->render('admin_index');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
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

	public function testAdminEdit() {
		$this->Contacts->request->params['action'] = 'admin_edit';
		$this->Contacts->request->params['url']['url'] = 'admin/contacts/edit';
		$this->Contacts->Session->write('Auth.User', array(
			'id' => 1,
			'username' => 'admin',
		));
		$this->Contacts->request->data = array(
			'Contact' => array(
				'id' => 1,
				'title' => 'Contact [modified]',
			),
		);
		$this->Contacts->startupProcess();
		$this->Contacts->admin_edit();
		$this->assertEqual($this->Contacts->redirectUrl, array('action' => 'index'));

		$contact = $this->Contacts->Contact->findByAlias('contact');
		$this->assertEqual($contact['Contact']['title'], 'Contact [modified]');

		$this->Contacts->testView = true;
		$output = $this->Contacts->render('admin_edit');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
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

	public function testView() {
		$this->Contacts->request->params['action'] = 'view';
		$this->Contacts->request->params['url']['url'] = 'contacts/view/contact';
		$this->Contacts->request->data = array(
			'Message' => array(
				'name' => 'John Smith',
				'email' => 'john.smith@example.com',
				'title' => 'Hello',
				'body' => 'text here',
			),
		);
		$this->Contacts->startupProcess();
		$this->Contacts->view('contact');
		$this->assertEqual($this->Contacts->viewVars['continue'], true);

		$this->Contacts->testView = true;
		$output = $this->Contacts->render('view');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}

	public function endTest() {
		$this->Contacts->Session->destroy();
		unset($this->Contacts);
		ClassRegistry::flush();
	}
}
?>