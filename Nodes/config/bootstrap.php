<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Core\Croogo;
use Croogo\Wysiwyg\Wysiwyg;

$cacheConfig = array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['nodes']]
);
Cache::setConfig('nodes', $cacheConfig);
Cache::setConfig('nodes_view', $cacheConfig);
Cache::setConfig('nodes_promoted', $cacheConfig);
Cache::setConfig('nodes_term', $cacheConfig);
Cache::setConfig('nodes_index', $cacheConfig);

Croogo::hookApiComponent('Croogo/Nodes.Nodes', 'Nodes.NodeApi');
Croogo::hookComponent('*', [
    'NodesHook' => [
        'className' => 'Croogo/Nodes.Nodes'
    ]
]);

Croogo::hookHelper('*', 'Croogo/Nodes.Nodes');

// Configure Wysiwyg
Wysiwyg::setActions([
    'Croogo/Nodes.Admin/Nodes/add' => [
        [
            'elements' => '#NodeBody',
        ],
        [
            'elements' => '#NodeExcerpt',
        ],
    ],
    'Croogo/Nodes.Admin/Nodes/edit' => [
        [
            'elements' => '#NodeBody',
        ],
        [
            'elements' => '#NodeExcerpt',
        ],
    ],
    'Croogo/Translate.Admin/Translate/edit' => [
        [
            'elements' => "[id^='translations'][id$='body']",
        ],
        [
            'elements' => "[id^='translations'][id$='excerpt']",
        ],
    ],
]);

Croogo::translateModel('Croogo/Nodes.Nodes', [
    'fields' => [
        'title',
        'excerpt',
        'body',
    ],
]);
