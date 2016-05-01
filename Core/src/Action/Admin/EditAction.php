<?php

namespace Croogo\Core\Action\Admin;

use Cake\Event\Event;
use Crud\Action\EditAction as CrudEditAction;

class EditAction extends CrudEditAction
{
    protected function _handle()
    {
        parent::_handle();

        $this->_controller()->set([
            'editFields' => $this->config('editFields'),
        ]);
    }
}
