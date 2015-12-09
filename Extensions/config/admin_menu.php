<?php

namespace Croogo\Extensions\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'extensions', [
    'icon' => 'magic',
    'title' => __d('croogo', 'Extensions'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Extensions',
        'controller' => 'ExtensionsPlugins',
        'action' => 'index',
    ],
    'weight' => 35,
    'children' => [
        'themes' => [
            'title' => __d('croogo', 'Themes'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Extensions',
                'controller' => 'ExtensionsThemes',
                'action' => 'index',
            ],
            'weight' => 10,
        ],
        'locales' => [
            'title' => __d('croogo', 'Locales'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Extensions',
                'controller' => 'ExtensionsLocales',
                'action' => 'index',
            ],
            'weight' => 20,
        ],
        'plugins' => [
            'title' => __d('croogo', 'Plugins'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Extensions',
                'controller' => 'ExtensionsPlugins',
                'action' => 'index',
            ],
            'htmlAttributes' => [
                'class' => 'separator',
            ],
            'weight' => 30,
        ],
    ],
]);
