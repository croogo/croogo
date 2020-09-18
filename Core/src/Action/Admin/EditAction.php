<?php
declare(strict_types=1);

namespace Croogo\Core\Action\Admin;

use Crud\Action\EditAction as CrudEditAction;

class EditAction extends CrudEditAction
{
    protected function _get($id = null): void
    {
        parent::_get($id);

        $this->_controller()->set([
            'editFields' => $this->getConfig('editFields'),
        ]);
    }
}
