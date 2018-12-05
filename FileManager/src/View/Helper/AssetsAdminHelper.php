<?php

namespace Croogo\FileManager\View\Helper;

class AssetsAdminHelper extends AppHelper {

    public $helpers = array(
        'Html',
    );

    public function beforeRender($viewFile) {
        if (empty($this->request->params['admin'])) {
            return;
        }

        if ($this->_View->theme === 'AdminExtras') {
            $this->Html->css(array(
                'bootstrap-editable',
            ), array(
                'inline' => false,
            ));
            $this->Html->script(array(
                'bootstrap-editable.min',
            ), array(
                'block' => 'scriptBottom',
            ));
        }
    }

}
