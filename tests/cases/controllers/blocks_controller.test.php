<?php
App::import('Controller', 'Blocks');

class TestBlocksController extends BlocksController {

    public $name = 'Blocks';

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

class BlocksControllerTestCase extends CakeTestCase {

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
        $this->Blocks = new TestBlocksController();
        $this->Blocks->constructClasses();
        $this->Blocks->params['controller'] = 'blocks';
        $this->Blocks->params['pass'] = array();
        $this->Blocks->params['named'] = array();
    }

    public function testAdminIndex() {
        $this->Blocks->params['action'] = 'admin_index';
        $this->Blocks->params['url']['url'] = 'admin/blocks';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);
        $this->Blocks->admin_index();

        $this->Blocks->testView = true;
        $output = $this->Blocks->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
        $this->Blocks->params['action'] = 'admin_add';
        $this->Blocks->params['url']['url'] = 'admin/blocks/add';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->data = array(
            'Block' => array(
                'title' => 'Test block',
                'alias' => 'test_block',
                'show_title' => 'test_block',
                'region_id' => 4, // right
                'body' => 'text here',
                'visibility_paths' => '',
                'status' => 1,
            ),
            'Role' => array(
                'Role' => array(),
            ),
        );
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);
        $this->Blocks->admin_add();
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));

        $testBlock = $this->Blocks->Block->findByAlias('test_block');
        $this->assertEqual($testBlock['Block']['title'], 'Test block');

        $this->Blocks->testView = true;
        $output = $this->Blocks->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Blocks->params['action'] = 'admin_edit';
        $this->Blocks->params['url']['url'] = 'admin/blocks/edit';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->data = array(
            'Block' => array(
                'id' => 3, // About
                'title' => 'About [modified]',
                'visibility_paths' => '',
            ),
            'Role' => array(
                'Role' => array(),
            ),
        );
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);
        $this->Blocks->admin_edit();
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));

        $about = $this->Blocks->Block->findByAlias('about');
        $this->assertEqual($about['Block']['title'], 'About [modified]');

        $this->Blocks->testView = true;
        $output = $this->Blocks->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Blocks->params['action'] = 'admin_delete';
        $this->Blocks->params['url']['url'] = 'admin/blocks/delete';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);
        $this->Blocks->admin_delete(8); // ID of Search
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));

        $hasAny = $this->Blocks->Block->hasAny(array(
            'Block.alias' => 'search',
        ));
        $this->assertFalse($hasAny);
    }

    public function testAdminMoveUp() {
        $this->Blocks->params['action'] = 'admin_moveup';
        $this->Blocks->params['url']['url'] = 'admin/blocks/moveup/3';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);

        $this->Blocks->admin_moveup(3); // About
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
        $list = $this->Blocks->Block->find('list', array(
            'fields' => array(
                'id',
                'alias',
            ),
            'order' => 'Block.weight ASC',
        ));
        $blockAliases = array_values($list);
        $this->assertEqual($blockAliases, array(
            'about',
            'search',
            'categories',
            'blogroll',
            'recent_posts',
            'meta',
        ));
    }

    public function testAdminMoveUpWithSteps() {
        $this->Blocks->params['action'] = 'admin_moveup';
        $this->Blocks->params['url']['url'] = 'admin/blocks/moveup/6/3';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);

        $this->Blocks->admin_moveup(6, 3); // Blogroll up 3 steps
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
        $list = $this->Blocks->Block->find('list', array(
            'fields' => array(
                'id',
                'alias',
            ),
            'order' => 'Block.weight ASC',
        ));
        $blockAliases = array_values($list);
        $this->assertEqual($blockAliases, array(
            'blogroll',
            'search',
            'about',
            'categories',
            'recent_posts',
            'meta',
        ));
    }

    public function testAdminMoveDown() {
        $this->Blocks->params['action'] = 'admin_movedown';
        $this->Blocks->params['url']['url'] = 'admin/blocks/movedown/3';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);

        $this->Blocks->admin_movedown(3); // About
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
        $list = $this->Blocks->Block->find('list', array(
            'fields' => array(
                'id',
                'alias',
            ),
            'order' => 'Block.weight ASC',
        ));
        $blockAliases = array_values($list);
        $this->assertEqual($blockAliases, array(
            'search',
            'categories',
            'about',
            'blogroll',
            'recent_posts',
            'meta',
        ));
    }

    public function testAdminMoveDownWithSteps() {
        $this->Blocks->params['action'] = 'admin_movedown';
        $this->Blocks->params['url']['url'] = 'admin/blocks/movedown/8/3';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);

        $this->Blocks->admin_movedown(8, 2); // Search down 2 steps
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
        $list = $this->Blocks->Block->find('list', array(
            'fields' => array(
                'id',
                'alias',
            ),
            'order' => 'Block.weight ASC',
        ));
        $blockAliases = array_values($list);
        $this->assertEqual($blockAliases, array(
            'about',
            'categories',
            'search',
            'blogroll',
            'recent_posts',
            'meta',
        ));
    }

    public function testAdminProcessDelete() {
        $this->Blocks->params['action'] = 'admin_process';
        $this->Blocks->params['url']['url'] = 'admin/blocks/process';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);

        $this->Blocks->data['Block'] = array(
            'action' => 'delete',
            '8' => array( // Search
                'id' => 0,
            ),
            '3' => array( // About
                'id' => 1,
            ),
            '7' => array( // Categories
                'id' => 0,
            ),
            '6' => array( // Blogroll
                'id' => 1,
            ),
            '9' => array( // Recent Posts
                'id' => 0,
            ),
            '5' => array( // Meta
                'id' => 1,
            ),
        );
        $this->Blocks->admin_process();
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
        $list = $this->Blocks->Block->find('list', array(
            'fields' => array(
                'id',
                'alias',
            ),
            'order' => 'Block.weight ASC',
        ));
        $blockAliases = array_values($list);
        $this->assertEqual($blockAliases, array(
            'search',
            'categories',
            'recent_posts',
        ));
    }

    public function testAdminProcessPublish() {
        $this->Blocks->params['action'] = 'admin_process';
        $this->Blocks->params['url']['url'] = 'admin/blocks/process';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);

        // unpublish a Block for testing
        $this->Blocks->Block->id = 3; // About
        $this->Blocks->Block->save(array(
            'id' => 3,
            'status' => 0,
        ));
        $this->Blocks->Block->id = false;
        $about = $this->Blocks->Block->hasAny(array(
            'id' => 3,
            'status' => 0,
        ));
        $this->assertTrue($about);

        $this->Blocks->data['Block'] = array(
            'action' => 'publish',
            '8' => array( // Search
                'id' => 1,
            ),
            '3' => array( // About
                'id' => 1,
            ),
            '7' => array( // Categories
                'id' => 1,
            ),
            '6' => array( // Blogroll
                'id' => 1,
            ),
            '9' => array( // Recent Posts
                'id' => 1,
            ),
            '5' => array( // Meta
                'id' => 1,
            ),
        );
        $this->Blocks->admin_process();
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
        $list = $this->Blocks->Block->find('list', array(
            'conditions' => array(
                'Block.status' => 1,
            ),
            'fields' => array(
                'id',
                'alias',
            ),
            'order' => 'Block.weight ASC',
        ));
        $blockAliases = array_values($list);
        $this->assertEqual($blockAliases, array(
            'search',
            'about',
            'categories',
            'blogroll',
            'recent_posts',
            'meta',
        ));
    }

    public function testAdminProcessUnpublish() {
        $this->Blocks->params['action'] = 'admin_process';
        $this->Blocks->params['url']['url'] = 'admin/blocks/process';
        $this->Blocks->Component->initialize($this->Blocks);
        $this->Blocks->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Blocks->beforeFilter();
        $this->Blocks->Component->startup($this->Blocks);

        $this->Blocks->data['Block'] = array(
            'action' => 'unpublish',
            '8' => array( // Search
                'id' => 1,
            ),
            '3' => array( // About
                'id' => 1,
            ),
            '7' => array( // Categories
                'id' => 0,
            ),
            '6' => array( // Blogroll
                'id' => 1,
            ),
            '9' => array( // Recent Posts
                'id' => 0,
            ),
            '5' => array( // Meta
                'id' => 1,
            ),
        );
        $this->Blocks->admin_process();
        $this->assertEqual($this->Blocks->redirectUrl, array('action' => 'index'));
        $list = $this->Blocks->Block->find('list', array(
            'conditions' => array(
                'Block.status' => 1,
            ),
            'fields' => array(
                'id',
                'alias',
            ),
            'order' => 'Block.weight ASC',
        ));
        $blockAliases = array_values($list);
        $this->assertEqual($blockAliases, array(
            'categories',
            'recent_posts',
        ));
    }

    public function endTest() {
        $this->Blocks->Session->destroy();
        unset($this->Blocks);
        ClassRegistry::flush();
    }
}
?>