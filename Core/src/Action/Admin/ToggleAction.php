<?php

namespace Croogo\Core\Action\Admin;

use Crud\Action\BaseAction;
use Exception;

class ToggleAction extends BaseAction
{
    protected $_defaultConfig = [
        'enabled' => true,
        'field' => 'status'
    ];

    /**
     * Toggle Link status
     *
     * @param int $id Link id
     * @param int $status Current Link status
     * @throws Exception
     * @return void
     */
    protected function _post($id = null, $status = null)
    {
        if (empty($id) || $status === null) {
            throw new Exception(__d('croogo', 'Invalid content'));
        }

        $status = (int)!$status;

        $this->_controller()->viewBuilder()->setLayout('ajax');
        $this->_controller()->viewBuilder()->template('Croogo/Core./Common/admin_toggle');

        $entity = $this->_table()->get($id);
        $entity->set($this->config('field'), $status);
        if (!$this->_table()->save($entity)) {
            throw new Exception(__d('croogo', 'Failed toggling field %s to %s', $this->config('field'), $status));
        }

        $this->_controller()->set(compact('id', 'status'));
    }
}
