<?php
App::import('Helper', array(
    'Html',
    'Form',
    'Session',
    'Js',
    'Layout',
));
App::import('Component', 'Session');

class TheLayoutTestController extends Controller {
    var $name = 'TheTest';
    var $uses = null;
}

class HtmlHelperTest extends CakeTestCase {

    function startTest() {
        $view =& new View(new TheLayoutTestController());
        ClassRegistry::addObject('view', $view);
        $this->Layout =& new LayoutHelper();
        $this->Layout->Html =& new HtmlHelper();
        $this->Layout->Form =& new FormHelper();
        $this->Layout->Session =& new SessionHelper();
        $this->Layout->Js =& new JsHelper('JsBase');
        $this->Layout->Js->Html =& new HtmlHelper();
        $this->Layout->Js->Form =& new FormHelper();
        $this->Layout->Js->Form->Html =& new HtmlHelper();
        $this->Layout->Js->JsBaseEngine =& new JsBaseEngineHelper();
        $this->Layout->params = array(
            'controller' => 'nodes',
            'action' => 'index',
            'named' => array(),
        );
        $this->_appEncoding = Configure::read('App.encoding');
        $this->_asset = Configure::read('Asset');
        $this->_debug = Configure::read('debug');
    }

    function testJs() {
        $this->assertTrue(strstr($this->Layout->js(), 'var Croogo = {"basePath":"\/","params":{"controller":"nodes","action":"index","named":[]}};'));
    
        $this->Layout->params['locale'] = 'eng';
        $this->assertTrue(strstr($this->Layout->js(), 'var Croogo = {"basePath":"\/eng\/","params":{"controller":"nodes","action":"index","named":[]}};'));
        unset($this->Layout->params['locale']);

        Configure::write('Js.my_var', '123');
        $this->assertTrue(strstr($this->Layout->js(), 'var Croogo = {"basePath":"\/","params":{"controller":"nodes","action":"index","named":[]},"my_var":"123"};'));
        
        Configure::write('Js.my_var2', '456');
        $this->assertTrue(strstr($this->Layout->js(), 'var Croogo = {"basePath":"\/","params":{"controller":"nodes","action":"index","named":[]},"my_var":"123","my_var2":"456"};'));
    }

    function testStatus() {
        $this->assertEqual($this->Layout->status(true), $this->Layout->Html->image('/img/icons/tick.png'));
        $this->assertEqual($this->Layout->status(1), $this->Layout->Html->image('/img/icons/tick.png'));
        $this->assertEqual($this->Layout->status(false), $this->Layout->Html->image('/img/icons/cross.png'));
        $this->assertEqual($this->Layout->status(0), $this->Layout->Html->image('/img/icons/cross.png'));
    }

    function testIsLoggedIn() {
        $session =& new SessionComponent();
        $session->delete('Auth');
        $this->assertFalse($this->Layout->isLoggedIn());

        $session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
        ));
        $this->assertTrue($this->Layout->isLoggedIn());
        $session->delete('Auth');
    }

    function testGetRoleId() {
        $session =& new SessionComponent();
        $session->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
            'role_id' => 1,
        ));
        $this->assertEqual($this->Layout->getRoleId(), 1);

        $session->delete('Auth');
        $this->assertEqual($this->Layout->getRoleId(), 3);
    }

    function testRegionIsEmpty() {
        $this->assertTrue($this->Layout->regionIsEmpty('right'));

        $this->Layout->View->viewVars['blocks_for_layout'] = array(
            'right' => array(
                '0' => array('block here'),
                '1' => array('block here'),
                '2' => array('block here'),
            ),
        );
        $this->assertFalse($this->Layout->regionIsEmpty('right'));
    }

    function testLinkStringToArray() {
        $this->assertEqual($this->Layout->linkStringToArray('controller:nodes/action:index'), array(
            'plugin' => null,
            'controller' => 'nodes',
            'action' => 'index',
        ));
        $this->assertEqual($this->Layout->linkStringToArray('controller:nodes/action:index/pass/pass2'), array(
            'plugin' => null,
            'controller' => 'nodes',
            'action' => 'index',
            'pass',
            'pass2',
        ));
        $this->assertEqual($this->Layout->linkStringToArray('controller:nodes/action:index/param:value'), array(
            'plugin' => null,
            'controller' => 'nodes',
            'action' => 'index',
            'param' => 'value',
        ));
        $this->assertEqual($this->Layout->linkStringToArray('controller:nodes/action:index/with-slash/'), array(
            'plugin' => null,
            'controller' => 'nodes',
            'action' => 'index',
            'with-slash',
        ));
    }

    function endTest() {
        Configure::write('App.encoding', $this->_appEncoding);
        Configure::write('Asset', $this->_asset);
        Configure::write('debug', $this->_debug);
        ClassRegistry::flush();
        unset($this->Layout);
    }

}
?>