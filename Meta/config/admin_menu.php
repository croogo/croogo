<?php

namespace Croogo\Menus\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'settings.children.meta', [
    'title' => __d('croogo', 'Meta'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Meta',
        'controller' => 'Meta',
        'action' => 'index',
    ],
    'weight' => 20,
]);
