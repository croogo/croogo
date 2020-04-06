<?php

use Cake\Event\Event;
use Cake\Event\EventManager;

// When baking models, use the Cake standard table template, but also;
//   include the Search behavior, and
//   remove the Phinxlog association if present
//     this shows up in models with the same name as their plugin
//     after a Phinx migration has been run
EventManager::instance()->on(
    'Bake.beforeRender.Model.table',
    function (Event $event) {
        $view = $event->getSubject();

        $behaviors = $view->get('behaviors');
        $behaviors['Search.Search'] = [];
        $view->set('behaviors', $behaviors);

        $associations = $view->get('associations');
        foreach ($associations['belongsToMany'] as $key => $assoc) {
            if ($assoc['alias'] == 'Phinxlog') {
                unset($associations['belongsToMany'][$key]);
                $view->set('associations', $associations);
            }
        }
    }
);
