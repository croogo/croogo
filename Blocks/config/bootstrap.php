<?php

use Cake\Core\Configure;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Croogo;

CroogoCache::config('croogo_blocks', array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['blocks']]
));

Croogo::hookComponent('*', [
    'BlocksHook' => [
        'className' => 'Croogo/Blocks.Blocks',
        'priority' => 9,
    ]
]);

Croogo::hookHelper('*', 'Croogo/Blocks.Regions');

Croogo::mergeConfig('Translate.tables.Blocks', [
    'fields' => [
        'title' => 'titleTranslation',
        'body' => 'bodyTranslation',
    ],
    'translateTable' => 'Croogo/Blocks.Blocks',
]);
