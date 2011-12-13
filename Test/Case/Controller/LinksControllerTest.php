<?php
App::import('Controller', 'Links');

class TestLinksController extends LinksController {

    public $name = 'Links';

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

App::uses('CroogoTestCase', 'TestSuite');

class LinksControllerTest extends CroogoTestCase {

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
        $this->Links = new TestLinksController($request, $response);
        $this->Links->constructClasses();
        $this->Links->request->params['controller'] = 'links';
        $this->Links->request->params['pass'] = array();
        $this->Links->request->params['named'] = array();
    }

    public function testAdminIndex() {
        $this->Links->request->params['action'] = 'admin_index';
        $this->Links->request->params['url']['url'] = 'admin/links';
        $this->Links->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Links->startupProcess();

        $this->Links->admin_index();
        $this->assertEqual($this->Links->redirectUrl, array(
            'controller' => 'menus',
            'action' => 'index',
        ));

        $mainMenu = $this->Links->Link->Menu->findByAlias('main');
        $this->Links->admin_index($mainMenu['Menu']['id']);
        $this->assertEqual($this->Links->viewVars['menu'], $mainMenu);

        $this->Links->testView = true;
        $output = $this->Links->render('admin_index');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminAdd() {
        $this->Links->request->params['action'] = 'admin_add';
        $this->Links->request->params['url']['url'] = 'admin/links/add';
        $this->Links->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $mainMenu = ClassRegistry::init('Menu')->findByAlias('main');
        $this->Links->request->data = array(
            'Link' => array(
                'menu_id' => $mainMenu['Menu']['id'],
                'title' => 'Test link',
                'link' => '#test-link',
                'status' => 1,
            ),
            'Role' => array(
                'Role' => array(),
            ),
        );
        $this->Links->startupProcess();
        $this->Links->admin_add($mainMenu['Menu']['id']);
        $this->assertEqual($this->Links->redirectUrl, array('action' => 'index', $mainMenu['Menu']['id']));

        $testLink = $this->Links->Link->findByLink('#test-link');
        $this->assertEqual($testLink['Link']['title'], 'Test link');

        $this->Links->testView = true;
        $output = $this->Links->render('admin_add');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminEdit() {
        $this->Links->request->params['action'] = 'admin_edit';
        $this->Links->request->params['url']['url'] = 'admin/links/edit';
        $this->Links->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $homeLink = ClassRegistry::init('Link')->find('first', array(
            'conditions' => array(
                'Link.title' => 'Home',
                'Link.link' => '/',
            ),
        ));
        $this->Links->request->data = array(
            'Link' => array(
                'id' => $homeLink['Link']['id'],
                'menu_id' => $homeLink['Link']['menu_id'],
                'title' => 'Home [modified]',
                'link' => '/',
                'status' => 1,
            ),
            'Role' => array(
                'Role' => array(),
            ),
        );
        $this->Links->startupProcess();
        $this->Links->admin_edit($homeLink['Link']['id']);
        $this->assertEqual($this->Links->redirectUrl, array('action' => 'index', $homeLink['Link']['menu_id']));

        $link = $this->Links->Link->findById($homeLink['Link']['id']);
        $this->assertEqual($link['Link']['title'], 'Home [modified]');

        $this->Links->testView = true;
        $output = $this->Links->render('admin_edit');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }

    public function testAdminDelete() {
        $this->Links->request->params['action'] = 'admin_delete';
        $this->Links->request->params['url']['url'] = 'admin/links/delete';
        $this->Links->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $homeLink = ClassRegistry::init('Link')->find('first', array(
            'conditions' => array(
                'Link.title' => 'Home',
                'Link.link' => '/',
            ),
        ));
        $this->Links->startupProcess();
        $this->Links->admin_delete($homeLink['Link']['id']);
        $this->assertEqual($this->Links->redirectUrl, array('action' => 'index', $homeLink['Link']['menu_id']));

        $hasAny = $this->Links->Link->hasAny(array(
            'Link.title' => 'Home',
            'Link.link' => '/',
        ));
        $this->assertFalse($hasAny);
    }

    public function testAdminMoveUp() {
        $this->Links->request->params['action'] = 'admin_moveup';
        $this->Links->request->params['url']['url'] = 'admin/links/moveup';
        $this->Links->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));

        $mainMenu = ClassRegistry::init('Menu')->findByAlias('main');
        $aboutLink = ClassRegistry::init('Link')->find('first', array(
            'conditions' => array(
                'Link.menu_id' => $mainMenu['Menu']['id'],
                'Link.title' => 'About',
                'Link.link' => '/about',
            ),
        ));
        $this->Links->startupProcess();

        $this->Links->admin_moveup($aboutLink['Link']['id']);
        $this->assertEqual($this->Links->redirectUrl, array('action' => 'index', $mainMenu['Menu']['id']));
        $list = $this->Links->Link->generateTreeList(array(
            'Link.menu_id' => $mainMenu['Menu']['id'],
            'Link.status' => 1,
        ));
        $linkTitles = array_values($list);
        $this->assertEqual($linkTitles, array(
            'About',
            'Home',
            'Contact'
        ));
    }

    public function testAdminMoveUpWithSteps() {
        $this->Links->request->params['action'] = 'admin_moveup';
        $this->Links->request->params['url']['url'] = 'admin/links/moveup';
        $this->Links->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));

        $mainMenu = ClassRegistry::init('Menu')->findByAlias('main');
        $contactLink = ClassRegistry::init('Link')->find('first', array(
            'conditions' => array(
                'Link.menu_id' => $mainMenu['Menu']['id'],
                'Link.title' => 'Contact',
            ),
        ));
        $this->Links->startupProcess();

        $this->Links->admin_moveup($contactLink['Link']['id'], 2);
        $this->assertEqual($this->Links->redirectUrl, array('action' => 'index', $mainMenu['Menu']['id']));
        $list = $this->Links->Link->generateTreeList(array(
            'Link.menu_id' => $mainMenu['Menu']['id'],
            'Link.status' => 1,
        ));
        $linkTitles = array_values($list);
        $this->assertEqual($linkTitles, array(
            'Contact',
            'Home',
            'About',
        ));
    }

    public function testAdminMoveDown() {
        $this->Links->request->params['action'] = 'admin_movedown';
        $this->Links->request->params['url']['url'] = 'admin/links/movedown';
        $this->Links->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));

        $mainMenu = ClassRegistry::init('Menu')->findByAlias('main');
        $aboutLink = ClassRegistry::init('Link')->find('first', array(
            'conditions' => array(
                'Link.menu_id' => $mainMenu['Menu']['id'],
                'Link.title' => 'About',
                'Link.link' => '/about',
            ),
        ));
        $this->Links->startupProcess();

        $this->Links->admin_movedown($aboutLink['Link']['id']);
        $this->assertEqual($this->Links->redirectUrl, array('action' => 'index', $mainMenu['Menu']['id']));
        $list = $this->Links->Link->generateTreeList(array(
            'Link.menu_id' => $mainMenu['Menu']['id'],
            'Link.status' => 1,
        ));
        $linkTitles = array_values($list);
        $this->assertEqual($linkTitles, array(
            'Home',
            'Contact',
            'About',
        ));
    }

    public function testAdminMoveDownWithSteps() {
        $this->Links->request->params['action'] = 'admin_movedown';
        $this->Links->request->params['url']['url'] = 'admin/links/movedown';
        $this->Links->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));

        $mainMenu = ClassRegistry::init('Menu')->findByAlias('main');
        $homeLink = ClassRegistry::init('Link')->find('first', array(
            'conditions' => array(
                'Link.menu_id' => $mainMenu['Menu']['id'],
                'Link.title' => 'Home',
            ),
        ));
        $this->Links->startupProcess();

        $this->Links->admin_movedown($homeLink['Link']['id'], 2);
        $this->assertEqual($this->Links->redirectUrl, array('action' => 'index', $mainMenu['Menu']['id']));
        $list = $this->Links->Link->generateTreeList(array(
            'Link.menu_id' => $mainMenu['Menu']['id'],
            'Link.status' => 1,
        ));
        $linkTitles = array_values($list);
        $this->assertEqual($linkTitles, array(
            'About',
            'Contact',
            'Home',
        ));
    }

    public function endTest() {
        $this->Links->Session->destroy();
        unset($this->Links);
        ClassRegistry::flush();
    }
}
?>