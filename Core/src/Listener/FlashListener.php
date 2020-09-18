<?php
declare(strict_types=1);

namespace Croogo\Core\Listener;

use Cake\Core\Configure;
use Cake\Event\Event;
use Crud\Listener\BaseListener;

class FlashListener extends BaseListener
{
    public function implementedEvents(): array
    {
        return [
            'Crud.setFlash' => ['callable' => 'onSetFlash'],
        ];
    }

    public function onSetFlash(Event $event)
    {
        $subject = $event->getSubject();
        $type = !empty($subject->params['type']) ? $subject->params['type'] : 'error';

        $plugin = Configure::read('Site.admin_theme');
        $subject->element = $plugin . '.' . $type;
    }
}
