<?php

namespace Croogo\Comments\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'content.children.comments', [
    'title' => __d('croogo', 'Comments'),
    'url' => [
        'admin' => true,
        'plugin' => 'Croogo/Comments',
        'controller' => 'Comments',
        'action' => 'index',
    ],
    'children' => [
        'published' => [
            'title' => __d('croogo', 'Published'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Comments',
                'controller' => 'Comments',
                'action' => 'index',
                '?' => [
                    'status' => '1',
                ],
            ],
        ],
        'approval' => [
            'title' => __d('croogo', 'Approval'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Comments',
                'controller' => 'Comments',
                'action' => 'index',
                '?' => [
                    'status' => '0',
                ],
            ],
        ],
    ],
]);
