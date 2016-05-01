<?php

namespace Croogo\Core\Action\Admin;

use Cake\Event\Event;
use Crud\Action\IndexAction as CrudIndexAction;

class IndexAction extends CrudIndexAction
{
    protected function _handle()
    {
        parent::_handle();

        $this->_controller()->set([
            'displayFields' => $this->config('displayFields'),
            'searchFields' => $this->config('searchFields'),
        ]);
    }
}
