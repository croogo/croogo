<?php

namespace Croogo\Core\Listener;

use Cake\Event\Event;
use Crud\Listener\BaseListener;

class ChooserListener extends BaseListener
{
    public function beforeFilter(Event $event)
    {
        if (!$this->_controller()->request->query('chooser')) {
            return;
        }

        $this->_controller()->viewBuilder()
            ->layout('Croogo/Core.admin_popup')
            ->template('chooser');
    }
}
