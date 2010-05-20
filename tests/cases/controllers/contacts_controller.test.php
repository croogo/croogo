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

class ContactsControllerTestCase extends CakeTestCase {

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
        $this->Contacts = new TestContactsController();
        $this->Contacts->constructClasses();
        $this->Contacts->params['controller'] = 'contacts';
        $this->Contacts->params['pass'] = array();
        $this->Contacts->params['named'] = array();
    }

    public function testAdminIndex() {
        $this->Contacts->params['action'] = 'admin_index';
        $this->Contacts->params['url']['url'] = 'admin/contacts';
        $this->Contacts->Component->initialize($this->Contacts);
        $this->Contacts->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Contacts->beforeFilter();
        $this->Contacts->Component->startup($this->Contacts);
        $this->Contacts->admin_index();

        $this->Contacts->testView = true;
        $output = $this->Contacts->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
        $this->Contacts->params['action'] = 'admin_add';
        $this->Contacts->params['url']['url'] = 'admin/contacts/add';
        $this->Contacts->Component->initialize($this->Contacts);
        $this->Contacts->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Contacts->data = array(
            'Contact' => array(
                'title' => 'New contact',
                'alias' => 'new_contact',
            ),
        );
        $this->Contacts->beforeFilter();
        $this->Contacts->Component->startup($this->Contacts);
        $this->Contacts->admin_add();
        $this->assertEqual($this->Contacts->redirectUrl, array('action' => 'index'));

        $newContact = $this->Contacts->Contact->findByAlias('new_contact');
        $this->assertEqual($newContact['Contact']['title'], 'New contact');

        $this->Contacts->testView = true;
        $output = $this->Contacts->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Contacts->params['action'] = 'admin_edit';
        $this->Contacts->params['url']['url'] = 'admin/contacts/edit';
        $this->Contacts->Component->initialize($this->Contacts);
        $this->Contacts->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Contacts->data = array(
            'Contact' => array(
                'id' => 1,
                'title' => 'Contact [modified]',
            ),
        );
        $this->Contacts->beforeFilter();
        $this->Contacts->Component->startup($this->Contacts);
        $this->Contacts->admin_edit();
        $this->assertEqual($this->Contacts->redirectUrl, array('action' => 'index'));

        $contact = $this->Contacts->Contact->findByAlias('contact');
        $this->assertEqual($contact['Contact']['title'], 'Contact [modified]');

        $this->Contacts->testView = true;
        $output = $this->Contacts->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Contacts->params['action'] = 'admin_delete';
        $this->Contacts->params['url']['url'] = 'admin/contacts/delete';
        $this->Contacts->Component->initialize($this->Contacts);
        $this->Contacts->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Contacts->beforeFilter();
        $this->Contacts->Component->startup($this->Contacts);
        $this->Contacts->admin_delete(1);
        $this->assertEqual($this->Contacts->redirectUrl, array('action' => 'index'));
        
        $hasAny = $this->Contacts->Contact->hasAny(array(
            'Contact.alias' => 'contact',
        ));
        $this->assertFalse($hasAny);
    }

    public function testView() {
        $this->Contacts->params['action'] = 'view';
        $this->Contacts->params['url']['url'] = 'contacts/view/contact';
        $this->Contacts->Component->initialize($this->Contacts);
        $this->Contacts->beforeFilter();
        $this->Contacts->Component->startup($this->Contacts);

        $this->Contacts->data = array(
            'Message' => array(
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'title' => 'Hello',
                'body' => 'text here',
            ),
        );
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