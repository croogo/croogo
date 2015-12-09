<?php

namespace Croogo\Contacts\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'contacts', [
    'icon' => 'comments',
    'title' => __d('croogo', 'Contacts'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Contacts',
        'controller' => 'Contacts',
        'action' => 'index',
    ],
    'weight' => 50,
    'children' => [
        'contacts' => [
            'title' => __d('croogo', 'Contacts'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Contacts',
                'controller' => 'Contacts',
                'action' => 'index',
            ],
        ],
        'messages' => [
            'title' => __d('croogo', 'Messages'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Contacts',
                'controller' => 'Messages',
                'action' => 'index',
            ],
        ],
    ],
]);
