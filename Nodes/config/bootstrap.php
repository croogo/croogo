<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Cache\Cache;
use Croogo\Core\Croogo;

$cacheConfig = array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['nodes']]
);
Cache::config('nodes', $cacheConfig);
Cache::config('nodes_view', $cacheConfig);
Cache::config('nodes_promoted', $cacheConfig);
Cache::config('nodes_term', $cacheConfig);
Cache::config('nodes_index', $cacheConfig);

Croogo::hookApiComponent('Croogo/Nodes.Nodes', 'Nodes.NodeApi');
Croogo::hookComponent('*', [
    'NodesHook' => [
        'className' => 'Croogo/Nodes.Nodes'
    ]
]);

Croogo::hookHelper('*', 'Croogo/Nodes.Nodes');

// Configure Wysiwyg
Croogo::mergeConfig('Wysiwyg.actions', [
    'Croogo/Nodes.Admin/Nodes/add' => [
        [
            'elements' => 'NodeBody',
        ],
    ],
    'Croogo/Nodes.Admin/Nodes/edit' => [
        [
            'elements' => 'NodeBody',
        ],
    ],
    'Croogo/Translate.Admin/Translate/edit' => [
        [
            'elements' => 'NodeBody',
        ],
    ],
], true);

Croogo::mergeConfig('Translate.models.Node', [
    'fields' => [
        'title' => 'titleTranslation',
        'excerpt' => 'excerptTranslation',
        'body' => 'bodyTranslation',
    ],
    'translateModel' => 'Nodes.Node',
]);
