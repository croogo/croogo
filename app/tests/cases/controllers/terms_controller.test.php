<?php
App::import('Controller', 'Terms');

class TestTermsController extends TermsController {

    var $name = 'Terms';

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

class TermsControllerTestCase extends CakeTestCase {

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
        $this->Terms = new TestTermsController();
        $this->Terms->constructClasses();
        $this->Terms->params['named'] = array();
        $this->Terms->params['controller'] = 'terms';
    }

    function testAdminAdd() {
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
    }

    function testAdminEdit() {
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
    }

    function testAdminDelete() {
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

    function testAdminMove() {
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

    function __testAdminMoveUp() {
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

    function __testAdminMoveUpWithSteps() {
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

    function __testAdminMoveDown() {
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

    function __testAdminMoveDownWithSteps() {
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

    function testAdminProcess() {
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
    }

    function __testAdminProcessUnpublish() {
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

    function __testAdminProcessPublish() {
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

    function endTest() {
        $this->Terms->Session->destroy();
        unset($this->Terms);
        ClassRegistry::flush();
    }
}
?>