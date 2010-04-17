<?php
App::import('Controller', 'Languages');

class TestLanguagesController extends LanguagesController {

    var $name = 'Languages';

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

    function __securityError() {

    }
}

class LanguagesControllerTestCase extends CakeTestCase {

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
        $this->Languages = new TestLanguagesController();
        $this->Languages->constructClasses();
        $this->Languages->params['controller'] = 'languages';
    }

    function testAdminAdd() {
        $this->Languages->params['action'] = 'admin_add';
        $this->Languages->params['url']['url'] = 'admin/languages/add';
        $this->Languages->Component->initialize($this->Languages);
        $this->Languages->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Languages->data = array(
            'Language' => array(
                'title' => 'Bengali',
                'alias' => 'ben',
            ),
        );
        $this->Languages->beforeFilter();
        $this->Languages->Component->startup($this->Languages);
        $this->Languages->admin_add();
        $this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));

        $ben = $this->Languages->Language->findByAlias('ben');
        $this->assertEqual($ben['Language']['title'], 'Bengali');
    }

    function testAdminEdit() {
        $this->Languages->params['action'] = 'admin_edit';
        $this->Languages->params['url']['url'] = 'admin/languages/edit';
        $this->Languages->Component->initialize($this->Languages);
        $this->Languages->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Languages->data = array(
            'Language' => array(
                'id' => 1,
                'title' => 'English [modified]',
                'alias' => 'eng',
            ),
        );
        $this->Languages->beforeFilter();
        $this->Languages->Component->startup($this->Languages);
        $this->Languages->admin_edit();
        $this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));

        $eng = $this->Languages->Language->findByAlias('eng');
        $this->assertEqual($eng['Language']['title'], 'English [modified]');
    }

    function testAdminDelete() {
        $this->Languages->params['action'] = 'admin_delete';
        $this->Languages->params['url']['url'] = 'admin/languages/delete';
        $this->Languages->Component->initialize($this->Languages);
        $this->Languages->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Languages->beforeFilter();
        $this->Languages->Component->startup($this->Languages);
        $this->Languages->admin_delete(1); // ID of English
        $this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));

        $hasAny = $this->Languages->Language->hasAny(array(
            'Language.alias' => 'eng',
        ));
        $this->assertFalse($hasAny);
    }

    function testAdminMove() {
        $this->Languages->params['action'] = 'admin_moveup';
        $this->Languages->params['url']['url'] = 'admin/languages/moveup';
        $this->Languages->Component->initialize($this->Languages);
        $this->Languages->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Languages->beforeFilter();
        $this->Languages->Component->startup($this->Languages);

        $this->__testAdminMoveUp();
        $this->__testAdminMoveUpWithSteps();

        $this->__testAdminMoveDown();
        $this->__testAdminMoveDownWithSteps();
    }

    function __testAdminMoveUp() {
        // add language
        $this->Languages->Language->save(array(
            'title' => 'Bengali',
            'alias' => 'ben',
        ));
        $benId = $this->Languages->Language->id;
        $this->assertEqual($benId, 2, __('Could not add a new language.', true));

        // get current list with order
        $list = $this->Languages->Language->find('list', array(
            'order' => 'Language.weight ASC',
        ));
        $this->assertEqual($list, array(
            '1' => 'English',
            '2' => 'Bengali',
        ));

        // move up
        $this->Languages->admin_moveup($benId);
        $this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));
        $list = $this->Languages->Language->find('list', array(
            'order' => 'Language.weight ASC',
        ));
        $this->assertEqual($list, array(
            '2' => 'Bengali',
            '1' => 'English',
        ));
    }

    function __testAdminMoveUpWithSteps() {
        // add another language
        $this->Languages->Language->id = false;
        $this->Languages->Language->save(array(
            'title' => 'German',
            'alias' => 'deu',
        ));
        $deuId = $this->Languages->Language->id;
        $this->assertEqual($deuId, 3, __('Could not add a new language.', true));

        // get current list with order
        $list = $this->Languages->Language->find('list', array(
            'order' => 'Language.weight ASC',
        ));
        $this->assertEqual($list, array(
            '2' => 'Bengali',
            '1' => 'English',
            '3' => 'German',
        ));

        // move up with steps
        $this->Languages->admin_moveup($deuId, 2);
        $this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));
        $list = $this->Languages->Language->find('list', array(
            'order' => 'Language.weight ASC',
        ));
        $this->assertEqual($list, array(
            '3' => 'German',
            '2' => 'Bengali',
            '1' => 'English',
        ));
    }

    function __testAdminMoveDown() {
        $this->Languages->admin_movedown(3);
        $list = $this->Languages->Language->find('list', array(
            'order' => 'Language.weight ASC',
        ));
        $this->assertEqual($list, array(
            '2' => 'Bengali',
            '3' => 'German',
            '1' => 'English',
        ));
    }

    function __testAdminMoveDownWithSteps() {
        $this->Languages->admin_movedown(2, 2);
        $list = $this->Languages->Language->find('list', array(
            'order' => 'Language.weight ASC',
        ));
        $this->assertEqual($list, array(
            '3' => 'German',
            '1' => 'English',
            '2' => 'Bengali',
        ));
    }

    function testAdminSelect() {
        $this->Languages->params['action'] = 'admin_select';
        $this->Languages->params['url']['url'] = 'admin/languages/select';
        $this->Languages->Component->initialize($this->Languages);
        $this->Languages->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Languages->beforeFilter();
        $this->Languages->Component->startup($this->Languages);

        $this->Languages->admin_select();
        $this->assertEqual($this->Languages->redirectUrl, array('action' => 'index'));

        $this->Languages->admin_select(1, 'Node');
        $this->assertEqual($this->Languages->viewVars['id'], 1);
        $this->assertEqual($this->Languages->viewVars['modelAlias'], 'Node');
        $this->assertEqual($this->Languages->viewVars['languages']['0']['Language']['title'], 'English');
        $this->assertEqual($this->Languages->viewVars['languages']['0']['Language']['alias'], 'eng');
    }

    function endTest() {
        $this->Languages->Session->destroy();
        unset($this->Languages);
        ClassRegistry::flush();
    }
}
?>