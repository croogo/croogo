<?php
App::uses('TypesController', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class TestTypesController extends TypesController {

    public $name = 'Types';

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

class TypesControllerTest extends CroogoTestCase {

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
        $this->Types = new TestTypesController($request, $response);
        $this->Types->constructClasses();
        $this->Types->request->params['controller'] = 'types';
        $this->Types->request->params['pass'] = array();
        $this->Types->request->params['named'] = array();
    }

    function testAdminIndex() {
        $this->Types->request->params['action'] = 'admin_index';
        $this->Types->request->params['url']['url'] = 'admin/types';
        $this->Types->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Types->startupProcess();
        $this->Types->admin_index();

        $this->Types->testView = true;
        $output = $this->Types->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
        $this->Types->request->params['action'] = 'admin_add';
        $this->Types->request->params['url']['url'] = 'admin/types/add';
        $this->Types->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Types->request->data = array(
            'Type' => array(
                'title' => 'New Type',
                'alias' => 'new_type',
            ),
        );
        $this->Types->startupProcess();
        $this->Types->admin_add();
        $this->assertEqual($this->Types->redirectUrl, array('action' => 'index'));

        $newType = $this->Types->Type->findByAlias('new_type');
        $this->assertEqual($newType['Type']['title'], 'New Type');

        $this->Types->testView = true;
        $output = $this->Types->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Types->request->params['action'] = 'admin_edit';
        $this->Types->request->params['url']['url'] = 'admin/types/edit';
        $this->Types->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Types->request->data = array(
            'Type' => array(
                'id' => 1, // page
                'description' => '[modified]',
            ),
        );
        $this->Types->startupProcess();
        $this->Types->admin_edit();
        $this->assertEqual($this->Types->redirectUrl, array('action' => 'index'));

        $page = $this->Types->Type->findByAlias('page');
        $this->assertEqual($page['Type']['description'], '[modified]');

        $this->Types->testView = true;
        $output = $this->Types->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Types->request->params['action'] = 'admin_delete';
        $this->Types->request->params['url']['url'] = 'admin/types/delete';
        $this->Types->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Types->startupProcess();
        $this->Types->admin_delete(1); // ID of page
        $this->assertEqual($this->Types->redirectUrl, array('action' => 'index'));
        
        $hasAny = $this->Types->Type->hasAny(array(
            'Type.alias' => 'page',
        ));
        $this->assertFalse($hasAny);
    }

    public function endTest() {
        $this->Types->Session->destroy();
        unset($this->Types);
        ClassRegistry::flush();
    }
}
