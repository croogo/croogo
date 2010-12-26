<?php
App::import('Controller', 'Roles');

class TestRolesController extends RolesController {

    public $name = 'Roles';

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

class RolesControllerTestCase extends CakeTestCase {

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
        $this->Roles = new TestRolesController();
        $this->Roles->constructClasses();
        $this->Roles->params['controller'] = 'roles';
        $this->Roles->params['pass'] = array();
        $this->Roles->params['named'] = array();
        $this->Roles->Role->Aro->useDbConfig = 'test';
        $this->Roles->Role->Permission->useDbConfig = 'test';
    }

    public function testAdminIndex() {
        $this->Roles->params['action'] = 'admin_index';
        $this->Roles->params['url']['url'] = 'admin/roles';
        $this->Roles->Component->initialize($this->Roles);
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ));
        $this->Roles->beforeFilter();
        $this->Roles->Component->startup($this->Roles);
        $this->Roles->admin_index();

        $this->Roles->testView = true;
        $output = $this->Roles->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
        $this->Roles->params['action'] = 'admin_add';
        $this->Roles->params['url']['url'] = 'admin/roles/add';
        $this->Roles->Component->initialize($this->Roles);
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ));
        $this->Roles->data = array(
            'Role' => array(
                'title' => 'new_role',
                'alias' => 'new_role',
            ),
        );
        $this->Roles->beforeFilter();
        $this->Roles->Component->startup($this->Roles);
        $this->Roles->admin_add();
        $this->assertEqual($this->Roles->redirectUrl, array('action' => 'index'));

        $newRole = $this->Roles->Role->findByAlias('new_role');
        $this->assertEqual($newRole['Role']['title'], 'new_role');

        $this->Roles->testView = true;
        $output = $this->Roles->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Roles->params['action'] = 'admin_edit';
        $this->Roles->params['url']['url'] = 'admin/roles/edit';
        $this->Roles->Component->initialize($this->Roles);
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ));
        $this->Roles->data = array(
            'Role' => array(
                'id' => 2, // Registered
                'title' => 'Registered [modified]',
            ),
        );
        $this->Roles->beforeFilter();
        $this->Roles->Component->startup($this->Roles);
        $this->Roles->admin_edit();
        $this->assertEqual($this->Roles->redirectUrl, array('action' => 'index'));

        $registered = $this->Roles->Role->findByAlias('registered');
        $this->assertEqual($registered['Role']['title'], 'Registered [modified]');

        $this->Roles->testView = true;
        $output = $this->Roles->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Roles->params['action'] = 'admin_delete';
        $this->Roles->params['url']['url'] = 'admin/roles/delete';
        $this->Roles->Component->initialize($this->Roles);
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ));
        $this->Roles->beforeFilter();
        $this->Roles->Component->startup($this->Roles);
        $this->Roles->admin_delete(1); // ID of Admin
        $this->assertEqual($this->Roles->redirectUrl, array('action' => 'index'));
        
        $hasAny = $this->Roles->Role->hasAny(array(
            'Role.alias' => 'admin',
        ));
        $this->assertFalse($hasAny);
    }

    public function endTest() {
        $this->Roles->Session->destroy();
        unset($this->Roles);
        ClassRegistry::flush();
    }
}
?>