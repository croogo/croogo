<?php
App::import('Controller', 'Types');

class TestTypesController extends TypesController {

    var $name = 'Types';

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
}

class TypesControllerTestCase extends CakeTestCase {

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
        $this->Types = new TestTypesController();
        $this->Types->constructClasses();
        $this->Types->params['controller'] = 'types';
    }

    function testAdminAdd() {
        $this->Types->params['action'] = 'admin_add';
        $this->Types->params['url']['url'] = 'admin/types/add';
        $this->Types->Component->initialize($this->Types);
        $this->Types->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Types->data = array(
            'Type' => array(
                'title' => 'New Type',
                'alias' => 'new_type',
            ),
        );
        $this->Types->beforeFilter();
        $this->Types->Component->startup($this->Types);
        $this->Types->admin_add();
        $this->assertEqual($this->Types->redirectUrl, array('action' => 'index'));

        $newType = $this->Types->Type->findByAlias('new_type');
        $this->assertEqual($newType['Type']['title'], 'New Type');
    }

    function testAdminEdit() {
        $this->Types->params['action'] = 'admin_edit';
        $this->Types->params['url']['url'] = 'admin/types/edit';
        $this->Types->Component->initialize($this->Types);
        $this->Types->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Types->data = array(
            'Type' => array(
                'id' => 1, // page
                'description' => '[modified]',
            ),
        );
        $this->Types->beforeFilter();
        $this->Types->Component->startup($this->Types);
        $this->Types->admin_edit();
        $this->assertEqual($this->Types->redirectUrl, array('action' => 'index'));

        $page = $this->Types->Type->findByAlias('page');
        $this->assertEqual($page['Type']['description'], '[modified]');
    }

    function testAdminDelete() {
        $this->Types->params['action'] = 'admin_delete';
        $this->Types->params['url']['url'] = 'admin/types/delete';
        $this->Types->Component->initialize($this->Types);
        $this->Types->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Types->beforeFilter();
        $this->Types->Component->startup($this->Types);
        $this->Types->admin_delete(1); // ID of page
        $this->assertEqual($this->Types->redirectUrl, array('action' => 'index'));
        
        $hasAny = $this->Types->Type->hasAny(array(
            'Type.alias' => 'page',
        ));
        $this->assertFalse($hasAny);
    }

    function endTest() {
        $this->Types->Session->destroy();
        unset($this->Types);
        ClassRegistry::flush();
    }
}
?>