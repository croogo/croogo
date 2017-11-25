<?php

namespace Croogo\Menus\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'menus', [
    'icon' => 'sitemap',
    'title' => __d('croogo', 'Menus'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Menus',
        'controller' => 'Menus',
        'action' => 'index',
    ],
    'weight' => 20,
    'children' => [
        'menus' => [
            'title' => __d('croogo', 'Menus'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Menus',
                'controller' => 'Menus',
                'action' => 'index',
            ],
            'weight' => 10,
        ],
    ],
]);
