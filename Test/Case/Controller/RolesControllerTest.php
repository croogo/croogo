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

App::uses('CroogoTestCase', 'TestSuite');

class RolesControllerTest extends CroogoTestCase {

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
        $this->Roles = new TestRolesController($request, $response);
        $this->Roles->constructClasses();
        $this->Roles->Role->Aro->useDbConfig = $this->Roles->Role->useDbConfig;
        $this->Roles->request->params['controller'] = 'roles';
        $this->Roles->request->params['pass'] = array();
        $this->Roles->request->params['named'] = array();
    }

    public function testAdminIndex() {
        $this->Roles->request->params['action'] = 'admin_index';
        $this->Roles->request->params['url']['url'] = 'admin/roles';
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ));
        $this->Roles->startupProcess();
        $this->Roles->admin_index();

        $this->Roles->testView = true;
        $output = $this->Roles->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
        $this->Roles->request->params['action'] = 'admin_add';
        $this->Roles->request->params['url']['url'] = 'admin/roles/add';
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ));
        $this->Roles->request->data = array(
            'Role' => array(
                'title' => 'new_role',
                'alias' => 'new_role',
            ),
        );
        $this->Roles->startupProcess();
        $this->Roles->admin_add();
        $this->assertEqual($this->Roles->redirectUrl, array('action' => 'index'));

        $newRole = $this->Roles->Role->findByAlias('new_role');
        $this->assertEqual($newRole['Role']['title'], 'new_role');

        $this->Roles->testView = true;
        $output = $this->Roles->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Roles->request->params['action'] = 'admin_edit';
        $this->Roles->request->params['url']['url'] = 'admin/roles/edit';
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ));
        $this->Roles->request->data = array(
            'Role' => array(
                'id' => 2, // Registered
                'title' => 'Registered [modified]',
            ),
        );
        $this->Roles->startupProcess();
        $this->Roles->admin_edit();
        $this->assertEqual($this->Roles->redirectUrl, array('action' => 'index'));

        $registered = $this->Roles->Role->findByAlias('registered');
        $this->assertEqual($registered['Role']['title'], 'Registered [modified]');

        $this->Roles->testView = true;
        $output = $this->Roles->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Roles->request->params['action'] = 'admin_delete';
        $this->Roles->request->params['url']['url'] = 'admin/roles/delete';
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ));
        $this->Roles->startupProcess();
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
