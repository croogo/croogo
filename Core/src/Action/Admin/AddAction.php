<?php

namespace Croogo\Core\Action\Admin;

use Cake\Event\Event;
use Crud\Action\AddAction as CrudAddAction;

class AddAction extends CrudAddAction
{
    protected function _handle()
    {
        parent::_handle();

        $this->_controller()->set([
            'editFields' => $this->config('editFields'),
        ]);
    }
}
