<?php

namespace Croogo\Core\Action\Admin;

use Crud\Action\AddAction as CrudAddAction;

class AddAction extends CrudAddAction
{
    protected function _get()
    {
        parent::_get();

        $this->_controller()->set([
            'editFields' => $this->getConfig('editFields'),
        ]);
    }
}
