<?php
App::uses('CommentsController', 'Controller');
App::uses('CroogoControllerTestCase', 'TestSuite');

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

class CommentsControllerTest extends CroogoControllerTestCase {

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

/**
 * startTest
 */
    public function startTest() {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $request = new CakeRequest();
        $response = new CakeResponse();
        $this->Comments = new TestCommentsController($request, $response);
        $this->Comments->constructClasses();
        $this->Comments->request->params['controller'] = 'Comments';
        $this->Comments->request->params['pass'] = array();
        $this->Comments->request->params['named'] = array();
    }

/**
 * endTest
 */
    public function endTest() {
        unset($this->Comments);
        CakeSession::clear();
        ClassRegistry::flush();
    }

    public function testAdminIndex() {
        $this->Comments->request->params['action'] = 'admin_index';
        $this->Comments->request->params['url']['url'] = 'admin/comments';
        $this->Comments->Components->trigger('initialize', array(&$this->Comments));
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->Components->trigger('startup', array(&$this->Comments));
        $this->Comments->admin_index();

        $this->Comments->testView = true;
        $output = $this->Comments->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Comments->request->params['action'] = 'admin_edit';
        $this->Comments->request->params['url']['url'] = 'admin/comments/edit';
        $this->Comments->Components->trigger('initialize', array(&$this->Comments));
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->request->data = array(
            'Comment' => array(
                'id' => 1, // Mr Croogo
                'name' => 'Mr Croogo [modified]',
                'email' => 'contact@example.com',
                'body' => 'lots of text...',
            ),
        );
        $this->Comments->Components->trigger('startup', array(&$this->Comments));
        $this->Comments->admin_edit();
        $this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));

        $comment = $this->Comments->Comment->findById(1);
        $this->assertEqual($comment['Comment']['name'], 'Mr Croogo [modified]');

        $this->Comments->testView = true;
        $output = $this->Comments->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Comments->request->params['action'] = 'admin_delete';
        $this->Comments->request->params['url']['url'] = 'admin/comments/delete';
        $this->Comments->Components->trigger('initialize', array(&$this->Comments));
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->Components->trigger('startup', array(&$this->Comments));
        $this->Comments->admin_delete(1);
        $this->assertEqual($this->Comments->redirectUrl, array('action' => 'index'));

        $hasAny = $this->Comments->Comment->hasAny(array(
            'Comment.id' => 1,
        ));
        $this->assertFalse($hasAny);
    }

    public function testAdminProcessDelete() {
        $this->Comments->request->params['action'] = 'admin_process';
        $this->Comments->request->params['url']['url'] = 'admin/comments/process';
        $this->Comments->Components->trigger('initialize', array(&$this->Comments));
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->Components->trigger('startup', array(&$this->Comments));

        $this->Comments->request->data['Comment'] = array(
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
        $this->Comments->request->params['action'] = 'admin_process';
        $this->Comments->request->params['url']['url'] = 'admin/comments/process';
        $this->Comments->Components->trigger('initialize', array(&$this->Comments));
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->Components->trigger('startup', array(&$this->Comments));

        // unpublish a Comment for testing
        $this->Comments->Comment->id = 1;
        $this->Comments->Comment->saveField('status', 0);
        $this->Comments->Comment->id = false;
        $comment = $this->Comments->Comment->hasAny(array(
            'id' => 1,
            'status' => 0,
        ));
        $this->assertTrue($comment);

        $this->Comments->request->data['Comment'] = array(
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
        $this->Comments->request->params['action'] = 'admin_process';
        $this->Comments->request->params['url']['url'] = 'admin/comments/process';
        $this->Comments->Components->trigger('initialize', array(&$this->Comments));
        $this->Comments->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Comments->Components->trigger('startup', array(&$this->Comments));

        $this->Comments->request->data['Comment'] = array(
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

/**
 * testAdd
 */
    public function testAdd() {
        $Comments = $this->generate('Comments', array(
            'components' => array(
                'Email' => array('send'),
                'Session',
            ),
        ));
        $Comments->Email
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue(true));
        $Comments->request->params['action'] = 'add';
        $Comments->request->params['url']['url'] = 'comments/add';
        $Comments->Components->trigger('initialize', array(&$Comments));

        $Comments->Components->trigger('startup', array(&$Comments));
        $Comments->request->data['Comment'] = array(
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'website' => 'http://example.com',
            'body' => 'text here...',
        );
        $node = $Comments->Comment->Node->findBySlug('hello-world');
        $Comments->add($node['Node']['id']);
        $this->assertEqual($Comments->viewVars['success'], 1);

        $comments = $Comments->Comment->generateTreeList(array('Comment.node_id' => $node['Node']['id']), '{n}.Comment.id', '{n}.Comment.name');
        $commenters = array_values($comments);
        $this->assertEqual($commenters, array('Mr Croogo', 'John Smith'));

        $Comments->testView = true;
        $output = $Comments->render('add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

/**
 * testAddWithParent
 */
    public function testAddWithParent() {
        $Comments = $this->generate('Comments', array(
            'components' => array(
                'Email' => array('send'),
            ),
        ));
        $Comments->Email
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue(true));
        $Comments->request->params['action'] = 'add';
        $Comments->request->params['url']['url'] = 'comments/add';
        $Comments->Components->trigger('initialize', array(&$Comments));
        $Comments->Components->trigger('startup', array(&$Comments));


        $Comments->request->data['Comment'] = array(
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'website' => 'http://example.com',
            'body' => 'text here...',
        );
        $node = $Comments->Comment->Node->findBySlug('hello-world');
        $Comments->add($node['Node']['id'], 1); // under the comment by Mr Croogo
        $this->assertEqual($Comments->viewVars['success'], 1);

        $comments = $Comments->Comment->generateTreeList(array('Comment.node_id' => $node['Node']['id']), '{n}.Comment.id', '{n}.Comment.name');
        $commenters = array_values($comments);
        $this->assertEqual($commenters, array('Mr Croogo', '_John Smith'));

        $Comments->testView = true;
        $output = $Comments->render('add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }
}
