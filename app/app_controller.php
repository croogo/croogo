<?php
class AppController extends Controller {

    var $components = array(
        'Croogo',
        'Acl',
        'Auth',
        'Acl.AclFilter',
        'RequestHandler',
        'Email',
        'Akismet',
        'Recaptcha',
        'Theme',
    );
    var $helpers = array(
        'Html',
        'Form',
        'Text',
        'Javascript',
        'Time',
        'Layout',
        'Custom',
        'Meta',
        'Tinymce',
    );
    var $uses = array(
        'Block',
        'Link',
        'Setting',
        'Node',
    );
    var $view = 'Theme';
    var $theme;

    function beforeFilter() {
        $this->AclFilter->auth();
        $this->Setting->writeConfiguration();
        $this->RequestHandler->setContent('json', 'text/x-json');

        if (isset($this->params['admin'])) {
            $this->layout = 'admin';
        }

        if ($this->RequestHandler->isAjax()) {
            $this->layout = 'ajax';
        }

        if (Configure::read('Site.theme') && !isset($this->params['admin'])) {
            $this->theme = Configure::read('Site.theme');
        }

        if (!isset($this->params['admin']) && 
            Configure::read('Site.status') == 0) {
            $this->layout = 'maintenance';
            $this->pageTitle = __('Site down for maintenance', true);
            $this->render('../elements/blank');
        }

        if (isset($this->params['locale'])) {
            Configure::write('Config.language', $this->params['locale']);
        }
    }

}
?>