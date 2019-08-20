<?php

use Cake\Core\Configure;
use Cake\Cache\Cache;
use Croogo\Core\Croogo;

Cache::setConfig('croogo_blocks', array_merge(
    Cache::getConfig('default'),
    ['groups' => ['blocks']]
));

Croogo::hookComponent('*', [
    'BlocksHook' => [
        'className' => 'Croogo/Blocks.Blocks',
        'priority' => 9,
    ]
]);

Croogo::hookHelper('*', 'Croogo/Blocks.Regions');

Croogo::translateModel('Croogo/Blocks.Blocks', [
    'fields' => [
        'title',
        'body',
    ],
    'allowEmptyTranslations' => false,
]);
