<?php
declare(strict_types=1);

namespace Croogo\Core\Action\Admin;

use Crud\Action\AddAction as CrudAddAction;

class AddAction extends CrudAddAction
{
    protected function _get($id = null): void
    {
        parent::_get($id);

        $this->_controller()->set([
            'editFields' => $this->getConfig('editFields'),
        ]);
    }
}
