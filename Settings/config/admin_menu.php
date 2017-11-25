<?php

namespace Croogo\Settings\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'settings', [
    'icon' => 'cog',
    'title' => __d('croogo', 'Settings'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Settings',
        'controller' => 'Settings',
        'action' => 'prefix',
        'Site',
    ],
    'weight' => 60,
    'children' => [
        'site' => [
            'title' => __d('croogo', 'Site'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Settings',
                'controller' => 'Settings',
                'action' => 'prefix',
                'Site',
            ],
            'weight' => 10,
        ],

        'theme' => [
            'title' => __d('croogo', 'Theme'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Settings',
                'controller' => 'Settings',
                'action' => 'prefix',
                'Theme',
            ],
            'weight' => 15,
        ],

        'reading' => [
            'title' => __d('croogo', 'Reading'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Settings',
                'controller' => 'Settings',
                'action' => 'prefix',
                'Reading',
            ],
            'weight' => 30,
        ],

        'writing' => [
            'title' => __d('croogo', 'Writing'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Settings',
                'controller' => 'Settings',
                'action' => 'prefix',
                'Writing',
            ],
            'weight' => 40,
        ],

        'comment' => [
            'title' => __d('croogo', 'Comment'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Settings',
                'controller' => 'Settings',
                'action' => 'prefix',
                'Comment',
            ],
            'weight' => 50,
        ],

        'service' => [
            'title' => __d('croogo', 'Service'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Settings',
                'controller' => 'Settings',
                'action' => 'prefix',
                'Service',
            ],
            'weight' => 60,
        ],

        'languages' => [
            'title' => __d('croogo', 'Languages'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Settings',
                'controller' => 'Languages',
                'action' => 'index',
            ],
            'weight' => 70,
        ],

        'cache' => [
            'title' => __d('croogo', 'Cache'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Settings',
                'controller' => 'Caches',
                'action' => 'index',
            ],
            'weight' => 70,
        ],

    ],
]);
