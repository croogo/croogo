<?php

namespace Croogo\FileManager\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'media', [
    'icon' => 'image',
    'title' => __d('croogo', 'Media'),
    'url' => [
        'prefix' => 'Admin',
        'plugin' => 'Croogo/FileManager',
        'controller' => 'Attachments',
        'action' => 'index',
    ],
    'weight' => 40,
    'children' => [
        'attachments' => [
            'title' => __d('croogo', 'Attachments'),
            'url' => [
                'prefix' => 'Admin',
                'plugin' => 'Croogo/FileManager',
                'controller' => 'Attachments',
                'action' => 'index',
            ],
        ],
        'file_manager' => [
            'title' => __d('croogo', 'File Manager'),
            'url' => [
                'prefix' => 'Admin',
                'plugin' => 'Croogo/FileManager',
                'controller' => 'FileManager',
                'action' => 'browse',
            ],
        ],
    ],
]);
