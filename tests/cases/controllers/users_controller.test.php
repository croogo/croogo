<?php
App::import('Controller', 'Users');

class TestUsersController extends UsersController {

    public $name = 'Users';

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

class UsersControllerTestCase extends CakeTestCase {

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
        $this->Users = new TestUsersController();
        $this->Users->constructClasses();
        $this->Users->params['controller'] = 'users';
        $this->Users->params['pass'] = array();
        $this->Users->params['named'] = array();
    }

    function testAdminIndex() {
        $this->Users->params['action'] = 'admin_index';
        $this->Users->params['url']['url'] = 'admin/users';
        $this->Users->Component->initialize($this->Users);
        $this->Users->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Users->beforeFilter();
        $this->Users->Component->startup($this->Users);
        $this->Users->admin_index();

        $this->Users->testView = true;
        $output = $this->Users->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
        $this->Users->params['action'] = 'admin_add';
        $this->Users->params['url']['url'] = 'admin/users/add';
        $this->Users->Component->initialize($this->Users);
        $this->Users->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Users->data = array(
            'User' => array(
                'username' => 'new_user',
                'name' => 'New User',
            ),
        );
        $this->Users->beforeFilter();
        $this->Users->Component->startup($this->Users);
        $this->Users->admin_add();
        $this->assertEqual($this->Users->redirectUrl, array('action' => 'index'));

        $newUser = $this->Users->User->findByUsername('new_user');
        $this->assertEqual($newUser['User']['name'], 'New User');

        $this->Users->testView = true;
        $output = $this->Users->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Users->params['action'] = 'admin_edit';
        $this->Users->params['url']['url'] = 'admin/users/edit';
        $this->Users->Component->initialize($this->Users);
        $this->Users->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Users->data = array(
            'User' => array(
                'id' => 1, // admin
                'name' => 'Administrator [modified]',
            ),
        );
        $this->Users->beforeFilter();
        $this->Users->Component->startup($this->Users);
        $this->Users->admin_edit(1);
        $this->assertEqual($this->Users->redirectUrl, array('action' => 'index'));

        $admin = $this->Users->User->findByUsername('admin');
        $this->assertEqual($admin['User']['name'], 'Administrator [modified]');

        $this->Users->testView = true;
        $this->Users->params['pass']['0'] = 1;
        $output = $this->Users->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Users->params['action'] = 'admin_delete';
        $this->Users->params['url']['url'] = 'admin/users/delete';
        $this->Users->Component->initialize($this->Users);
        $this->Users->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Users->beforeFilter();
        $this->Users->Component->startup($this->Users);
        $this->Users->admin_delete(1); // ID of admin
        $this->assertEqual($this->Users->redirectUrl, array('action' => 'index'));
        
        $hasAny = $this->Users->User->hasAny(array(
            'User.username' => 'admin',
        ));
        $this->assertFalse($hasAny);
    }

    public function endTest() {
        $this->Users->Session->destroy();
        unset($this->Users);
        ClassRegistry::flush();
    }
}
?>