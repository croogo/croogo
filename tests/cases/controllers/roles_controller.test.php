<?php
App::import('Controller', 'Roles');

class TestRolesController extends RolesController {

    var $name = 'Roles';

    var $autoRender = false;

    function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }

    function render($action = null, $layout = null, $file = null) {
        $this->renderedAction = $action;
    }

    function _stop($status = 0) {
        $this->stopped = $status;
    }

    function __securityError() {

    }
}

class RolesControllerTestCase extends CakeTestCase {

    var $fixtures = array(
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
        'nodes_term',
        'region',
        'role',
        'setting',
        'term',
        'type',
        'types_vocabulary',
        'user',
        'vocabulary',
    );

    function startTest() {
        $this->Roles = new TestRolesController();
        $this->Roles->constructClasses();
        $this->Roles->params['controller'] = 'roles';
        $this->Roles->Role->Aro->useDbConfig = 'test';
        $this->Roles->Role->Permission->useDbConfig = 'test';
    }

    function testAdminAdd() {
        $this->Roles->params['action'] = 'admin_add';
        $this->Roles->params['url']['url'] = 'admin/roles/add';
        $this->Roles->Component->initialize($this->Roles);
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
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
    }

    function testAdminEdit() {
        $this->Roles->params['action'] = 'admin_edit';
        $this->Roles->params['url']['url'] = 'admin/roles/edit';
        $this->Roles->Component->initialize($this->Roles);
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
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
    }

    function testAdminDelete() {
        $this->Roles->params['action'] = 'admin_delete';
        $this->Roles->params['url']['url'] = 'admin/roles/delete';
        $this->Roles->Component->initialize($this->Roles);
        $this->Roles->Session->write('Auth.User', array(
            'id' => 1,
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

    function endTest() {
        $this->Roles->Session->destroy();
        unset($this->Roles);
        ClassRegistry::flush();
    }
}
?>