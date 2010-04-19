<?php
App::import('Controller', 'Vocabularies');

class TestVocabulariesController extends VocabulariesController {

    var $name = 'Vocabularies';

    var $autoRender = false;

    var $testView = false;

    function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }

    function render($action = null, $layout = null, $file = null) {
        if (!$this->testView) {
            $this->renderedAction = $action;
        } else {
            return parent::render($action, $layout, $file);
        }
    }

    function _stop($status = 0) {
        $this->stopped = $status;
    }

    function __securityError() {

    }
}

class VocabulariesControllerTestCase extends CakeTestCase {

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
        $this->Vocabularies = new TestVocabulariesController();
        $this->Vocabularies->constructClasses();
        $this->Vocabularies->params['controller'] = 'vocabularies';
        $this->Vocabularies->params['pass'] = array();
        $this->Vocabularies->params['named'] = array();
    }

    function testAdminIndex() {
        $this->Vocabularies->params['action'] = 'admin_index';
        $this->Vocabularies->params['url']['url'] = 'admin/vocabularies';
        $this->Vocabularies->Component->initialize($this->Vocabularies);
        $this->Vocabularies->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Vocabularies->beforeFilter();
        $this->Vocabularies->Component->startup($this->Vocabularies);
        $this->Vocabularies->admin_index();

        $this->Vocabularies->testView = true;
        $output = $this->Vocabularies->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    function testAdminAdd() {
        $this->Vocabularies->params['action'] = 'admin_add';
        $this->Vocabularies->params['url']['url'] = 'admin/vocabularies/add';
        $this->Vocabularies->Component->initialize($this->Vocabularies);
        $this->Vocabularies->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Vocabularies->data = array(
            'Vocabulary' => array(
                'title' => 'New Vocabulary',
                'alias' => 'new_vocabulary',
            ),
        );
        $this->Vocabularies->beforeFilter();
        $this->Vocabularies->Component->startup($this->Vocabularies);
        $this->Vocabularies->admin_add();
        $this->assertEqual($this->Vocabularies->redirectUrl, array('action' => 'index'));

        $newVocabulary = $this->Vocabularies->Vocabulary->findByAlias('new_vocabulary');
        $this->assertEqual($newVocabulary['Vocabulary']['title'], 'New Vocabulary');

        $this->Vocabularies->testView = true;
        $output = $this->Vocabularies->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    function testAdminEdit() {
        $this->Vocabularies->params['action'] = 'admin_edit';
        $this->Vocabularies->params['url']['url'] = 'admin/vocabularies/edit';
        $this->Vocabularies->Component->initialize($this->Vocabularies);
        $this->Vocabularies->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Vocabularies->data = array(
            'Vocabulary' => array(
                'id' => 1, // categories
                'title' => 'Categories [modified]',
            ),
        );
        $this->Vocabularies->beforeFilter();
        $this->Vocabularies->Component->startup($this->Vocabularies);
        $this->Vocabularies->admin_edit();
        $this->assertEqual($this->Vocabularies->redirectUrl, array('action' => 'index'));

        $categories = $this->Vocabularies->Vocabulary->findByAlias('categories');
        $this->assertEqual($categories['Vocabulary']['title'], 'Categories [modified]');

        $this->Vocabularies->testView = true;
        $output = $this->Vocabularies->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    function testAdminDelete() {
        $this->Vocabularies->params['action'] = 'admin_delete';
        $this->Vocabularies->params['url']['url'] = 'admin/vocabularies/delete';
        $this->Vocabularies->Component->initialize($this->Vocabularies);
        $this->Vocabularies->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Vocabularies->beforeFilter();
        $this->Vocabularies->Component->startup($this->Vocabularies);
        $this->Vocabularies->admin_delete(1); // ID of categories
        $this->assertEqual($this->Vocabularies->redirectUrl, array('action' => 'index'));
        
        $hasAny = $this->Vocabularies->Vocabulary->hasAny(array(
            'Vocabulary.alias' => 'categories',
        ));
        $this->assertFalse($hasAny);
    }

    function endTest() {
        $this->Vocabularies->Session->destroy();
        unset($this->Vocabularies);
        ClassRegistry::flush();
    }
}
?>