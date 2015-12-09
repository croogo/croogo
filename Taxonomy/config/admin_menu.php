<?php

namespace Croogo\Taxonomy\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'content.children.content_types', [
    'title' => __d('croogo', 'Content Types'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Taxonomy',
        'controller' => 'Types',
        'action' => 'index',
    ],
    'weight' => 30,
]);

Nav::add('sidebar', 'content.children.taxonomy', [
    'title' => __d('croogo', 'Taxonomy'),
    'url' => [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Taxonomy',
        'controller' => 'Vocabularies',
        'action' => 'index',
    ],
    'weight' => 40,
    'children' => [
        'list' => [
            'title' => __d('croogo', 'List'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Taxonomy',
                'controller' => 'Vocabularies',
                'action' => 'index',
            ],
            'weight' => 10,
        ],
        'add_new' => [
            'title' => __d('croogo', 'Add new'),
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Taxonomy',
                'controller' => 'Vocabularies',
                'action' => 'add',
            ],
            'weight' => 20,
            'htmlAttributes' => ['class' => 'separator'],
        ]
    ]
]);
