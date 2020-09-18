<?php
declare(strict_types=1);

namespace Croogo\Core\Action\Admin;

use Cake\Http\Response;
use Crud\Action\IndexAction as CrudIndexAction;

class IndexAction extends CrudIndexAction
{
    protected function _handle(): ?Response
    {
        $res = parent::_handle();

        $this->_controller()->set([
            'displayFields' => $this->getConfig('displayFields'),
            'searchFields' => $this->getConfig('searchFields'),
        ]);

        return $res;
    }
}
