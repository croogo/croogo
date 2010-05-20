<?php
App::import('Controller', 'Comments');

class TestCommentsController extends CommentsController {

    public $name = 'Comments';

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

class CommentsControllerTestCase extends CakeTestCase {

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
        $this->Comments = new TestCommentsController();
        $this->Comments->constructClasses();
        $this->Comments->params['controller'] = 'Comments';
        $this->Comments->params['pass'] = array();
        $this->Comments->params['named'] = array();
    }

    public function testAdminIndex() {
        $this->Comments->params['action'] = 'admin_index';
        $this->Comments->params['url']['url'] = 'admin/comments';
        $this->Comments->Component->initialize($this->Comments);
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->beforeFilter();
        $this->Comments->Component->startup($this->Comments);
        $this->Comments->admin_index();

        $this->Comments->testView = true;
        $output = $this->Comments->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Comments->params['action'] = 'admin_edit';
        $this->Comments->params['url']['url'] = 'admin/comments/edit';
        $this->Comments->Component->initialize($this->Comments);
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->data = array(
            'Comment' => array(
                'id' => 1, // Mr Croogo
                'name' => 'Mr Croogo [modified]',
                'email' => 'contact@example.com',
                'body' => 'lots of text...',
            ),
        );
        $this->Comments->beforeFilter();
        $this->Comments->Component->startup($this->Comments);
        $this->Comments->admin_edit();
        $this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));

        $comment = $this->Comments->Comment->findById(1);
        $this->assertEqual($comment['Comment']['name'], 'Mr Croogo [modified]');

        $this->Comments->testView = true;
        $output = $this->Comments->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Comments->params['action'] = 'admin_delete';
        $this->Comments->params['url']['url'] = 'admin/comments/delete';
        $this->Comments->Component->initialize($this->Comments);
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->beforeFilter();
        $this->Comments->Component->startup($this->Comments);
        $this->Comments->admin_delete(1);
        $this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));

        $hasAny = $this->Comments->Comment->hasAny(array(
            'Comment.id' => 1,
        ));
        $this->assertFalse($hasAny);
    }

    public function testAdminProcessDelete() {
        $this->Comments->params['action'] = 'admin_process';
        $this->Comments->params['url']['url'] = 'admin/comments/process';
        $this->Comments->Component->initialize($this->Comments);
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->beforeFilter();
        $this->Comments->Component->startup($this->Comments);

        $this->Comments->data['Comment'] = array(
            'action' => 'delete',
            '1' => array(
                'id' => 1,
            ),
        );
        $this->Comments->admin_process();
        $this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));
        $list = $this->Comments->Comment->find('list', array(
            'fields' => array(
                'id',
                'name',
            ),
            'order' => 'Comment.lft ASC',
        ));
        $this->assertEqual($list, array());
    }

    public function testAdminProcessPublish() {
        $this->Comments->params['action'] = 'admin_process';
        $this->Comments->params['url']['url'] = 'admin/comments/process';
        $this->Comments->Component->initialize($this->Comments);
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->beforeFilter();
        $this->Comments->Component->startup($this->Comments);

        // unpublish a Comment for testing
        $this->Comments->Comment->id = 1;
        $this->Comments->Comment->saveField('status', 0);
        $this->Comments->Comment->id = false;
        $comment = $this->Comments->Comment->hasAny(array(
            'id' => 1,
            'status' => 0,
        ));
        $this->assertTrue($comment);

        $this->Comments->data['Comment'] = array(
            'action' => 'publish',
            '1' => array(
                'id' => 1,
            ),
        );
        $this->Comments->admin_process();
        $this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));
        $list = $this->Comments->Comment->find('list', array(
            'conditions' => array(
                'Comment.status' => 1,
            ),
            'fields' => array(
                'id',
                'name',
            ),
            'order' => 'Comment.lft ASC',
        ));
        $this->assertEqual($list, array(
            '1' => 'Mr Croogo',
        ));
    }

    public function testAdminProcessUnpublish() {
        $this->Comments->params['action'] = 'admin_process';
        $this->Comments->params['url']['url'] = 'admin/comments/process';
        $this->Comments->Component->initialize($this->Comments);
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->beforeFilter();
        $this->Comments->Component->startup($this->Comments);

        $this->Comments->data['Comment'] = array(
            'action' => 'unpublish',
            '1' => array(
                'id' => 1,
            ),
        );
        $this->Comments->admin_process();
        $this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));
        $list = $this->Comments->Comment->find('list', array(
            'conditions' => array(
                'Comment.status' => 1,
            ),
            'fields' => array(
                'id',
                'name',
            ),
            'order' => 'Comment.lft ASC',
        ));
        $this->assertEqual($list, array());
    }

    public function testAdd() {
        $this->Comments->params['action'] = 'add';
        $this->Comments->params['url']['url'] = 'comments/add';
        $this->Comments->Component->initialize($this->Comments);
        $this->Comments->beforeFilter();
        $this->Comments->Component->startup($this->Comments);

        $this->Comments->data['Comment'] = array(
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'website' => 'http://example.com',
            'body' => 'text here...',
        );
        $node = $this->Comments->Comment->Node->findBySlug('hello-world');
        $this->Comments->add($node['Node']['id']);
        $this->assertTrue($this->Comments->viewVars['success']);

        $comments = $this->Comments->Comment->generatetreelist(array('Comment.node_id' => $node['Node']['id']), '{n}.Comment.id', '{n}.Comment.name');
        $commenters = array_values($comments);
        $this->assertEqual($commenters, array('Mr Croogo', 'John Smith'));

        $this->Comments->testView = true;
        $output = $this->Comments->render('add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAddWithParent() {
        $this->Comments->params['action'] = 'add';
        $this->Comments->params['url']['url'] = 'comments/add';
        $this->Comments->Component->initialize($this->Comments);
        $this->Comments->beforeFilter();
        $this->Comments->Component->startup($this->Comments);

        $this->Comments->data['Comment'] = array(
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'website' => 'http://example.com',
            'body' => 'text here...',
        );
        $node = $this->Comments->Comment->Node->findBySlug('hello-world');
        $this->Comments->add($node['Node']['id'], 1); // under the comment by Mr Croogo
        $this->assertTrue($this->Comments->viewVars['success']);

        $comments = $this->Comments->Comment->generatetreelist(array('Comment.node_id' => $node['Node']['id']), '{n}.Comment.id', '{n}.Comment.name');
        $commenters = array_values($comments);
        $this->assertEqual($commenters, array('Mr Croogo', '_John Smith'));

        $this->Comments->testView = true;
        $output = $this->Comments->render('add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function endTest() {
        $this->Comments->Session->destroy();
        unset($this->Comments);
        ClassRegistry::flush();
    }
}
?>