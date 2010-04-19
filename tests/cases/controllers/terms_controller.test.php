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
        $this->Terms->params['url']['url'] = 'admin/terms';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_index();

        $this->Terms->testView = true;
        $output = $this->Terms->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
        $this->Terms->params['named']['vocabulary'] = 1;
        $this->Terms->params['action'] = 'admin_add';
        $this->Terms->params['url']['url'] = 'admin/terms/add'; // categories
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->data = array(
            'Term' => array(
                'vocabulary_id' => 1, // categories
                'title' => 'New Term',
                'slug' => 'new-term',
            ),
        );
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_add();
        $this->assertEqual($this->Terms->redirectUrl, array(
            'action' => 'index',
            'vocabulary' => 1,
        ));

        $newTerm = $this->Terms->Term->findBySlug('new-term');
        $this->assertEqual($newTerm['Term']['title'], 'New Term');

        $this->Terms->testView = true;
        $output = $this->Terms->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Terms->params['named']['vocabulary'] = 1;
        $this->Terms->params['action'] = 'admin_edit';
        $this->Terms->params['url']['url'] = 'admin/terms/edit';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->data = array(
            'Term' => array(
                'id' => 1,
                'title' => 'Uncategorized [modified]',
            ),
        );
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_edit();
        $this->assertEqual($this->Terms->redirectUrl, array(
            'action' => 'index',
            'vocabulary' => 1,
        ));

        $uncategorized = $this->Terms->Term->findBySlug('uncategorized');
        $this->assertEqual($uncategorized['Term']['title'], 'Uncategorized [modified]');

        $this->Terms->testView = true;
        $output = $this->Terms->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Terms->params['named']['vocabulary'] = 1;
        $this->Terms->params['action'] = 'admin_delete';
        $this->Terms->params['url']['url'] = 'admin/terms/delete';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);
        $this->Terms->admin_delete(1); // ID of uncategorized
        $this->assertEqual($this->Terms->redirectUrl, array(
            'action' => 'index',
            'vocabulary' => 1,
        ));

        $hasAny = $this->Terms->Term->hasAny(array(
            'Term.slug' => 'uncategorized',
        ));
        $this->assertFalse($hasAny);
    }

    public function testAdminMove() {
        $this->Terms->params['named']['vocabulary'] = 1;
        $this->Terms->params['action'] = 'admin_moveup';
        $this->Terms->params['url']['url'] = 'admin/terms/moveup';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);

        $this->__testAdminMoveUp();
        $this->__testAdminMoveUpWithSteps();

        $this->__testAdminMoveDown();
        $this->__testAdminMoveDownWithSteps();
    }

    private function __testAdminMoveUp() {
        // get current list with order for categories
        $list = $this->Terms->Term->find('list', array(
            'conditions' => array(
                'Term.vocabulary_id' => 1,
            ),
            'order' => 'Term.lft ASC',
        ));
        $this->assertEqual($list, array(
            '1' => 'Uncategorized',
            '2' => 'Announcements',
        ));

        // move up
        $this->Terms->admin_moveup(2, 1);
        $this->assertEqual($this->Terms->redirectUrl, array(
            'action' => 'index',
            'vocabulary' => 1,
        ));
        $list = $this->Terms->Term->find('list', array(
            'conditions' => array(
                'Term.vocabulary_id' => 1,
            ),
            'order' => 'Term.lft ASC',
        ));
        $this->assertEqual($list, array(
            '2' => 'Announcements',
            '1' => 'Uncategorized',
        ));
    }

    private function __testAdminMoveUpWithSteps() {
        // add new term
        $this->Terms->Term->id = false;
        $this->Terms->Term->save(array(
            'vocabulary_id' => 1,
            'title' => 'New Term',
            'slug' => 'new-term',
        ));
        $newTermId = $this->Terms->Term->id;
        $this->Terms->newTermId = $newTermId;

        // get current list with order
        $list = $this->Terms->Term->find('list', array(
            'conditions' => array(
                'Term.vocabulary_id' => 1,
            ),
            'order' => 'Term.lft ASC',
        ));
        $this->assertEqual($list, array(
            '1' => 'Uncategorized',
            '2' => 'Announcements',
            $newTermId => 'New Term',
        ));

        // move up with steps
        $this->Terms->admin_moveup($newTermId, 2);
        $this->assertEqual($this->Terms->redirectUrl, array(
            'action' => 'index',
            'vocabulary' => 1,
        ));
        $list = $this->Terms->Term->find('list', array(
            'conditions' => array(
                'Term.vocabulary_id' => 1,
            ),
            'order' => 'Term.lft ASC',
        ));
        $this->assertEqual($list, array(
            $newTermId => 'New Term',
            '1' => 'Uncategorized',
            '2' => 'Announcements',
        ));
    }

    private function __testAdminMoveDown() {
        $this->Terms->admin_movedown(1);
        $list = $this->Terms->Term->find('list', array(
            'conditions' => array(
                'Term.vocabulary_id' => 1,
            ),
            'order' => 'Term.lft ASC',
        ));
        $this->assertEqual($list, array(
            $this->Terms->newTermId => 'New Term',
            '2' => 'Announcements',
            '1' => 'Uncategorized',
        ));
    }

    private function __testAdminMoveDownWithSteps() {
        $this->Terms->admin_movedown($this->Terms->newTermId, 2);
        $list = $this->Terms->Term->find('list', array(
            'conditions' => array(
                'Term.vocabulary_id' => 1,
            ),
            'order' => 'Term.lft ASC',
        ));
        $this->assertEqual($list, array(
            '2' => 'Announcements',
            '1' => 'Uncategorized',
            $this->Terms->newTermId => 'New Term',
        ));
    }

    public function testAdminProcess() {
        $this->Terms->params['named']['vocabulary'] = 1;
        $this->Terms->params['action'] = 'admin_process';
        $this->Terms->params['url']['url'] = 'admin/terms/process';
        $this->Terms->Component->initialize($this->Terms);
        $this->Terms->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Terms->beforeFilter();
        $this->Terms->Component->startup($this->Terms);

        $this->__testAdminProcessUnpublish();
        $this->__testAdminProcessPublish();
        $this->__testAdminProcessDelete();
    }

    private function __testAdminProcessUnpublish() {
        $this->Terms->data = array(
            'Term' => array(
                'action' => 'unpublish',
                '1' => array(
                    'id' => 1,
                ),
                '2' => array(
                    'id' => 1,
                ),
            ),
        );
        $this->Terms->admin_process();
        $this->assertEqual($this->Terms->redirectUrl, array(
            'action' => 'index',
            'vocabulary' => 1,
        ));

        $hasAny = $this->Terms->Term->hasAny(array(
            'Term.vocabulary_id' => 1,
            'Term.status' => 1,
        ));
        $this->assertFalse($hasAny);
    }

    private function __testAdminProcessPublish() {
        $this->Terms->data = array(
            'Term' => array(
                'action' => 'publish',
                '1' => array(
                    'id' => 1,
                ),
                '2' => array(
                    'id' => 1,
                ),
            ),
        );
        $this->Terms->admin_process();
        $this->assertEqual($this->Terms->redirectUrl, array(
            'action' => 'index',
            'vocabulary' => 1,
        ));

        $count = $this->Terms->Term->find('count', array(
            'conditions' => array(
                'Term.vocabulary_id' => 1,
                'Term.status' => 1,
            ),
        ));
        $this->assertTrue($count, 2);
    }

    private function __testAdminProcessDelete() {
        $this->Terms->data = array(
            'Term' => array(
                'action' => 'delete',
                '1' => array(
                    'id' => 1,
                ),
                '2' => array(
                    'id' => 1,
                ),
            ),
        );
        $this->Terms->admin_process();
        $this->assertEqual($this->Terms->redirectUrl, array(
            'action' => 'index',
            'vocabulary' => 1,
        ));

        $hasAny = $this->Terms->Term->hasAny(array(
            'Term.id' => array(1, 2),
        ));
        $this->assertFalse($hasAny);
    }

    public function endTest() {
        $this->Terms->Session->destroy();
        unset($this->Terms);
        ClassRegistry::flush();
    }
}
?>