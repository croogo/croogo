<?php

namespace Croogo\Core\Listener;

use Cake\Event\Event;
use Crud\Listener\BaseListener;

class ChooserListener extends BaseListener
{
    public function beforeFilter(Event $event)
    {
        if (!$this->_controller()->request->getQuery('chooser')) {
            return;
        }

        $this->_controller()->viewBuilder()
            ->setLayout('admin_popup');
    }
}
