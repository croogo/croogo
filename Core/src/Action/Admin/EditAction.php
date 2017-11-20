<?php

namespace Croogo\Core\Action\Admin;

use Cake\Event\Event;
use Crud\Action\EditAction as CrudEditAction;

class EditAction extends CrudEditAction
{
    protected function _get()
    {
        parent::_get();

        $this->_controller()->set([
            'editFields' => $this->config('editFields'),
        ]);
    }
}
