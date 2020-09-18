<?php
declare(strict_types=1);

namespace Croogo\Core\Listener;

use Cake\Event\Event;
use Crud\Listener\BaseListener;

class ChooserListener extends BaseListener
{
    public function beforeFilter(Event $event)
    {
        if (!$this->_controller()->getRequest()->getQuery('chooser')) {
            return;
        }

        $this->_controller()->viewBuilder()
            ->setLayout('admin_popup');
    }
}
