<?php

namespace Croogo\Nodes\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'content', [
    'icon' => 'edit',
    'title' => __d('croogo', 'Content'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Nodes',
        'controller' => 'Nodes',
        'action' => 'index',
    ],
    'weight' => 10,
    'children' => [
        'list' => [
            'title' => __d('croogo', 'List'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Nodes',
                'controller' => 'Nodes',
                'action' => 'index',
            ],
            'weight' => 10,
        ],
        'create' => [
            'title' => __d('croogo', 'Create'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Nodes',
                'controller' => 'Nodes',
                'action' => 'create',
            ],
            'weight' => 20,
        ],
    ]
]);
