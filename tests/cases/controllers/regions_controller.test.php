<?php
App::import('Controller', 'Regions');

class TestRegionsController extends RegionsController {

    var $name = 'Regions';

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

class RegionsControllerTestCase extends CakeTestCase {

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
        $this->Regions = new TestRegionsController();
        $this->Regions->constructClasses();
        $this->Regions->params['controller'] = 'regions';
    }

    function testAdminAdd() {
        $this->Regions->params['action'] = 'admin_add';
        $this->Regions->params['url']['url'] = 'admin/regions/add';
        $this->Regions->Component->initialize($this->Regions);
        $this->Regions->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Regions->data = array(
            'Region' => array(
                'title' => 'new_region',
                'alias' => 'new_region',
            ),
        );
        $this->Regions->beforeFilter();
        $this->Regions->Component->startup($this->Regions);
        $this->Regions->admin_add();
        $this->assertEqual($this->Regions->redirectUrl, array('action' => 'index'));

        $newRegion = $this->Regions->Region->findByAlias('new_region');
        $this->assertEqual($newRegion['Region']['title'], 'new_region');
    }

    function testAdminEdit() {
        $this->Regions->params['action'] = 'admin_edit';
        $this->Regions->params['url']['url'] = 'admin/regions/edit';
        $this->Regions->Component->initialize($this->Regions);
        $this->Regions->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Regions->data = array(
            'Region' => array(
                'id' => 4, // right
                'title' => 'right_modified',
            ),
        );
        $this->Regions->beforeFilter();
        $this->Regions->Component->startup($this->Regions);
        $this->Regions->admin_edit();
        $this->assertEqual($this->Regions->redirectUrl, array('action' => 'index'));

        $right = $this->Regions->Region->findByAlias('right');
        $this->assertEqual($right['Region']['title'], 'right_modified');
    }

    function testAdminDelete() {
        $this->Regions->params['action'] = 'admin_delete';
        $this->Regions->params['url']['url'] = 'admin/regions/delete';
        $this->Regions->Component->initialize($this->Regions);
        $this->Regions->Session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->Regions->beforeFilter();
        $this->Regions->Component->startup($this->Regions);
        $this->Regions->admin_delete(4); // ID of right
        $this->assertEqual($this->Regions->redirectUrl, array('action' => 'index'));
        
        $hasAny = $this->Regions->Region->hasAny(array(
            'Region.alias' => 'right',
        ));
        $this->assertFalse($hasAny);
    }

    function endTest() {
        $this->Regions->Session->destroy();
        unset($this->Regions);
        ClassRegistry::flush();
    }
}
?>