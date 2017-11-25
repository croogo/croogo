<?php

namespace Croogo\Extensions\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'extensions', [
    'icon' => 'magic',
    'title' => __d('croogo', 'Extensions'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Extensions',
        'controller' => 'Plugins',
        'action' => 'index',
    ],
    'weight' => 35,
    'children' => [
        'themes' => [
            'title' => __d('croogo', 'Themes'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Extensions',
                'controller' => 'Themes',
                'action' => 'index',
            ],
            'weight' => 10,
        ],
        'locales' => [
            'title' => __d('croogo', 'Locales'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Extensions',
                'controller' => 'Locales',
                'action' => 'index',
            ],
            'weight' => 20,
        ],
        'plugins' => [
            'title' => __d('croogo', 'Plugins'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Extensions',
                'controller' => 'Plugins',
                'action' => 'index',
            ],
            'htmlAttributes' => [
                'class' => 'separator',
            ],
            'weight' => 30,
        ],
    ],
]);
