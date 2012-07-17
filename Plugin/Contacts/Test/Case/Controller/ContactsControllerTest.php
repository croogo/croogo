<?php
App::uses('ContactsController', 'Contacts.Controller');
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
		'plugin.blocks.block',
		'plugin.comments.comment',
		'plugin.contacts.contact',
		'i18n',
		'plugin.settings.language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'plugin.contacts.message',
		'plugin.meta.meta',
		'plugin.contents.node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'plugin.settings.setting',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.term',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.users.user',
		'plugin.taxonomy.vocabulary',
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
		$this->Contacts->plugin = 'Contacts';
		$this->Contacts->constructClasses();
		$this->Contacts->Security = $this->getMock('SecurityComponent', null, array($this->Contacts->Components));
		$this->Contacts->request->params['plugin'] = 'contacts';
		$this->Contacts->request->params['controller'] = 'contacts';
		$this->Contacts->request->params['pass'] = array();
		$this->Contacts->request->params['named'] = array();

		$this->generate('Contacts', array(
			'methods' => array(
				'redirect',
				'_send_email',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->controller->plugin = 'Contacts';
		$this->controller->Auth
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
		$this->testAction('/admin/contacts/contacts/index');
		$this->assertNotEmpty($this->vars['contacts']);
	}

	public function testAdminAdd() {
		$this->Contacts->request->params['action'] = 'admin_add';
		$this->Contacts->request->params['url']['url'] = 'admin/contacts/contacts/add';
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
		$this->Contacts->Components->unload('Auth');
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
		$this->controller->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo('The Contact has been saved'),
				$this->equalTo('default'),
				$this->equalTo(array('class' => 'success'))
			);
		$this->controller
			->expects($this->once())
			->method('redirect');
		$this->testAction('/admin/contacts/contacts/edit/1', array(
			'data' => array(
				'Contact' => array(
					'id' => 1,
					'title' => 'Contact [modified]',
				),
			),
		));
		$result = $this->controller->Contact->findByAlias('contact');
		$this->assertEquals('Contact [modified]', $result['Contact']['title']);
	}

	public function testAdminDelete() {
		$this->Contacts->request->params['action'] = 'admin_delete';
		$this->Contacts->request->params['url']['url'] = 'admin/contacts/contacts/delete';
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
		$Contacts->plugin = 'Contacts';
		$Contacts->expects($this->once())
			->method('_send_email')
			->will($this->returnValue(true));
		$this->controller->request->params['action'] = 'view';
		$this->controller->request->params['url']['url'] = 'contacts/contacts/view/contact';
		$this->controller->request->data = array(
			'Message' => array(
				'name' => 'John Smith',
				'email' => 'john.smith@example.com',
				'title' => 'Hello',
				'body' => 'text here',
			),
		);
		$this->controller->startupProcess();
		$this->controller->view('contact');
		$this->assertEqual($this->controller->viewVars['continue'], true);

		$this->controller->testView = true;
		$output = $this->controller->render('view');
		$this->assertFalse(strpos($output, '<pre class="cake-debug">'));
	}
}
