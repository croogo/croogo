<?php

use Cake\Event\Event;
use Cake\Event\EventManager;

// When baking models, use the Cake standard table template,
// but include the Search behavior in all models.
EventManager::instance()->on(
    'Bake.beforeRender.Model.table',
    function (Event $event) {
        $view = $event->getSubject();
        $view->set('behaviors', ['Search.Search' => []]);
    }
);
