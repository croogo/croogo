<?php

namespace Croogo\Users\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'users', [
    'icon' => 'user',
    'title' => __d('croogo', 'Users'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Users',
        'controller' => 'Users',
        'action' => 'index',
    ],
    'weight' => 50,
    'children' => [
        'users' => [
            'title' => __d('croogo', 'Users'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Users',
                'controller' => 'Users',
                'action' => 'index',
            ],
            'weight' => 10,
        ],
        'roles' => [
            'title' => __d('croogo', 'Roles'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Users',
                'controller' => 'Roles',
                'action' => 'index',
            ],
            'weight' => 20,
        ],
    ],
]);
