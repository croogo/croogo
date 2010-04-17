<?php
App::import('Controller', 'Menus');

class TestMenusController extends MenusController {

    var $name = 'Menus';

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

class MenusControllerTestCase extends CakeTestCase {

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
        $this->Menus = new TestMenusController();
        $this->Menus->constructClasses();
        $this->Menus->params['controller'] = 'menus';
    }

    function testAdminAdd() {
        $this->Menus->params['action'] = 'admin_add';
        $this->Menus->params['url']['url'] = 'admin/menus/add';
        $this->Menus->Component->initialize($this->Menus);
        $this->Menus->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Menus->data = array(
            'Menu' => array(
                'title' => 'New Menu',
                'alias' => 'new',
            ),
        );
        $this->Menus->beforeFilter();
        $this->Menus->Component->startup($this->Menus);
        $this->Menus->admin_add();
        $this->assertEqual($this->Menus->redirectUrl, array('action' => 'index'));

        $newMenu = $this->Menus->Menu->findByAlias('new');
        $this->assertEqual($newMenu['Menu']['title'], 'New Menu');
    }

    function testAdminEdit() {
        $this->Menus->params['action'] = 'admin_edit';
        $this->Menus->params['url']['url'] = 'admin/menus/edit';
        $this->Menus->Component->initialize($this->Menus);
        $this->Menus->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Menus->data = array(
            'Menu' => array(
                'id' => 3, // main
                'title' => 'Main Menu [modified]',
            ),
        );
        $this->Menus->beforeFilter();
        $this->Menus->Component->startup($this->Menus);
        $this->Menus->admin_edit();
        $this->assertEqual($this->Menus->redirectUrl, array('action' => 'index'));

        $main = $this->Menus->Menu->findByAlias('main');
        $this->assertEqual($main['Menu']['title'], 'Main Menu [modified]');
    }

    function testAdminDelete() {
        $this->Menus->params['action'] = 'admin_delete';
        $this->Menus->params['url']['url'] = 'admin/menus/delete';
        $this->Menus->Component->initialize($this->Menus);
        $this->Menus->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Menus->beforeFilter();
        $this->Menus->Component->startup($this->Menus);
        $this->Menus->admin_delete(4); // ID of footer
        $this->assertEqual($this->Menus->redirectUrl, array('action' => 'index'));
        
        $hasAny = $this->Menus->Menu->hasAny(array(
            'Menu.alias' => 'footer',
        ));
        $this->assertFalse($hasAny);
    }

    function endTest() {
        $this->Menus->Session->destroy();
        unset($this->Menus);
        ClassRegistry::flush();
    }
}
?>