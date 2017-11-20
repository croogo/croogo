<?php

namespace Croogo\Core\Action\Admin;

use Cake\Event\Event;
use Crud\Action\AddAction as CrudAddAction;

class AddAction extends CrudAddAction
{
    protected function _get()
    {
        parent::_get();

        $this->_controller()->set([
            'editFields' => $this->config('editFields'),
        ]);
    }
}
