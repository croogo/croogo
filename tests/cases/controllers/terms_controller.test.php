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

    public function endTest() {
        $this->Terms->Session->destroy();
        unset($this->Terms);
        ClassRegistry::flush();
    }
}
?>