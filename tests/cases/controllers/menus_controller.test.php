<?php
App::import('Controller', 'Menus');

class TestMenusController extends MenusController {

    public $name = 'Menus';

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

class MenusControllerTestCase extends CakeTestCase {

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
        $this->Menus = new TestMenusController();
        $this->Menus->constructClasses();
        $this->Menus->params['controller'] = 'menus';
        $this->Menus->params['pass'] = array();
        $this->Menus->params['named'] = array();
    }

    public function testAdminIndex() {
        $this->Menus->params['action'] = 'admin_index';
        $this->Menus->params['url']['url'] = 'admin/menus';
        $this->Menus->Component->initialize($this->Menus);
        $this->Menus->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Menus->beforeFilter();
        $this->Menus->Component->startup($this->Menus);
        $this->Menus->admin_index();

        $this->Menus->testView = true;
        $output = $this->Menus->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
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

        $this->Menus->testView = true;
        $output = $this->Menus->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
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

        $this->Menus->testView = true;
        $output = $this->Menus->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
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

    public function endTest() {
        $this->Menus->Session->destroy();
        unset($this->Menus);
        ClassRegistry::flush();
    }
}
?>