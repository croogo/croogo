<?php

namespace Croogo\Acl\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'users.children.permissions', [
    'title' => __d('croogo', 'Permissions'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Acl',
        'controller' => 'Permissions',
        'action' => 'index',
    ],
    'weight' => 30,
]);

Nav::add('sidebar', 'settings.children.acl', [
    'title' => __d('croogo', 'Access Control'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Settings',
        'controller' => 'Settings',
        'action' => 'prefix',
        'Access Control',
    ],
]);
