<?php

namespace Croogo\FileManager\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'media', [
    'icon' => 'picture-o',
    'title' => __d('croogo', 'Media'),
    'url' => [
        'admin' => true,
        'plugin' => 'Croogo/FileManager',
        'controller' => 'Attachments',
        'action' => 'index',
    ],
    'weight' => 40,
    'children' => [
        'attachments' => [
            'title' => __d('croogo', 'Attachments'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/FileManager',
                'controller' => 'Attachments',
                'action' => 'index',
            ],
        ],
        'file_manager' => [
            'title' => __d('croogo', 'File Manager'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/FileManager',
                'controller' => 'FileManager',
                'action' => 'browse',
            ],
        ],
    ],
]);
