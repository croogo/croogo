<?php
App::import('Controller', 'Terms');

class TestTermsController extends TermsController {

    public $name = 'Terms';

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

class TermsControllerTestCase extends CakeTestCase {

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
        $this->Terms = new TestTermsController();
        $this->Terms->constructClasses();
        $this->Terms->params['named'] = array();
        $this->Terms->params['controller'] = 'terms';
        $this->Terms->params['pass'] = array();
        $this->Terms->params['named'] = array();
    }

    public function testAdminIndex() {
        $this->Terms->params['action'] = 'admin_index';
        $this->Terms->params['url']['url'] = 'admin/terms/index/1';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_index(1); // ID of categories

        $expectedTree = array(
            '1' => 'Uncategorized',
            '2' => 'Announcements',
        );
        $this->assertEqual($this->Terms->viewVars['termsTree'], $expectedTree);

        $this->Terms->testView = true;
        $output = $this->Terms->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
        $this->Terms->params['action'] = 'admin_add';
        $this->Terms->params['url']['url'] = 'admin/terms/add/1';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->data = array(
            'Taxonomy' => array(
                'parent_id' => null,
            ),
            'Term' => array(
                'title' => 'New Category',
                'slug' => 'new-category',
                'description' => 'category description here',
            ),
        );
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_add(1); // ID of categories
        $this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

        $termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
        $termsTreeSlugs = array_keys($termsTree);
        $expected = array(
            'uncategorized',
            'announcements',
            'new-category',
        );
        $this->assertEqual($termsTreeSlugs, $expected);

        $this->Terms->testView = true;
        $output = $this->Terms->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAddWithParent() {
        $this->Terms->params['action'] = 'admin_add';
        $this->Terms->params['url']['url'] = 'admin/terms/add/1';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->data = array(
            'Taxonomy' => array(
                'parent_id' => 1, // Uncategorized
            ),
            'Term' => array(
                'title' => 'New Category',
                'slug' => 'new-category',
                'description' => 'category description here',
            ),
        );
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_add(1); // ID of categories
        $this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

        $termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
        $termsTreeTitles = array_values($termsTree);
        $expected = array(
            'Uncategorized',
            '_New Category',
            'Announcements',
        );
        $this->assertEqual($termsTreeTitles, $expected);
    }

    public function testAdminEdit() {
        $this->Terms->params['action'] = 'admin_edit';
        $this->Terms->params['url']['url'] = 'admin/terms/edit';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->data = array(
            'Taxonomy' => array(
                'parent_id' => null,
            ),
            'Term' => array(
                'title' => 'New Category',
                'slug' => 'new-category',
                'description' => 'category description here',
            ),
        );
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_edit(1, 1); // ID of Uncategorized and Categories
        $this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

        $termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
        $expected = array(
            'new-category' => 'New Category',
            'announcements' => 'Announcements',
        );
        $this->assertEqual($termsTree, $expected);

        $this->Terms->testView = true;
        $output = $this->Terms->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Terms->params['action'] = 'admin_delete';
        $this->Terms->params['url']['url'] = 'admin/terms/delete';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_delete(1, 1); // ID of Uncategorized and Categories
        $this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

        $termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
        $expected = array(
            'announcements' => 'Announcements',
        );
        $this->assertEqual($termsTree, $expected);
    }

    public function testAdminMoveup() {
        $this->Terms->params['action'] = 'admin_moveup';
        $this->Terms->params['url']['url'] = 'admin/terms/moveup';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_moveup(2, 1); // ID of Announcements and Categories
        $this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

        $termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
        $expected = array(
            'announcements' => 'Announcements',
            'uncategorized' => 'Uncategorized',
        );
        $this->assertEqual($termsTree, $expected);
    }

    public function testAdminMovedown() {
        $this->Terms->params['action'] = 'admin_movedown';
        $this->Terms->params['url']['url'] = 'admin/terms/movedown';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_movedown(1, 1); // ID of Uncategorized and Categories
        $this->assertEqual($this->Terms->redirectUrl, array('action' => 'index', 1));

        $termsTree = $this->Terms->Term->Taxonomy->getTree('categories');
        $expected = array(
            'announcements' => 'Announcements',
            'uncategorized' => 'Uncategorized',
        );
        $this->assertEqual($termsTree, $expected);
    }

    public function endTest() {
        $this->Terms->Session->destroy();
        unset($this->Terms);
        ClassRegistry::flush();
    }
}
?>