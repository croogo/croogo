<?php

namespace Croogo\Core\Listener;

use Cake\Core\Configure;
use Cake\Event\Event;
use Crud\Listener\BaseListener;

class FlashListener extends BaseListener
{
    public function implementedEvents()
    {
        return [
            'Crud.setFlash' => ['callable' => 'onSetFlash'],
        ];
    }

    public function onSetFlash(Event $event)
    {
        $subject = $event->subject();
        $type = !empty($subject->params['type']) ? $subject->params['type'] : 'error';

        $plugin = Configure::read('Site.admin_theme');
        $subject->element = $plugin . '.' . $type;
    }

}
