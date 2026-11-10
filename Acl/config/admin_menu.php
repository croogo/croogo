<?php

namespace Croogo\Acl\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'users.children.permissions', [
    'title' => __d('croogo', 'Permissions'),
    'url' => [
        'prefix' => 'Admin',
        'plugin' => 'Croogo/Acl',
        'controller' => 'Permissions',
        'action' => 'index',
    ],
    'weight' => 30,
]);

