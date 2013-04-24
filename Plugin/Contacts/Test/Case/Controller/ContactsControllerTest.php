<?php
App::uses('ContactsController', 'Contacts.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');
App::uses('CroogoTestFixture', 'Croogo.TestSuite');

class ContactsControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
		'plugin.blocks.block',
		'plugin.comments.comment',
		'plugin.contacts.contact',
		'plugin.translate.i18n',
		'plugin.settings.language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'plugin.contacts.message',
		'plugin.meta.meta',
		'plugin.nodes.node',
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
		$this->ContactsController = $this->generate('Contacts.Contacts', array(
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
		unset($this->ContactsController);
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

/**
 * testAdminAdd
 *
 * @return void
 */
	public function testAdminAdd() {
		$this->expectFlashAndRedirect('The Contact has been saved');
		$this->testAction('/admin/contacts/contacts/add', array(
			'data' => array(
				'Contact' => array(
					'title' => 'New contact',
					'alias' => 'new_contact',
				),
			),
		));
		$newContact = $this->ContactsController->Contact->findByAlias('new_contact');
		$this->assertEqual($newContact['Contact']['title'], 'New contact');
	}

/**
 * testAdminEdit
 *
 * @return void
 */
	public function testAdminEdit() {
		$this->expectFlashAndRedirect('The Contact has been saved');
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

/**
 * testAdminDelete
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->expectFlashAndRedirect('Contact deleted');
		$this->testAction('admin/contacts/contacts/delete/1');
		$hasAny = $this->ContactsController->Contact->hasAny(array(
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
