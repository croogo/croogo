<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Core\Croogo;

$cacheConfig = array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['taxonomy']]
);
Cache::setConfig('croogo_types', $cacheConfig);
Cache::setConfig('croogo_vocabularies', $cacheConfig);

Croogo::hookComponent('*', 'Croogo/Taxonomy.Taxonomy');

Croogo::hookHelper('*', 'Croogo/Taxonomy.Taxonomies');

Croogo::translateModel('Croogo/Taxonomy.Terms', [
    'fields' => [
        'title',
        'description',
    ],
]);

Croogo::translateModel('Croogo/Taxonomy.Types', [
    'fields' => [
        'title',
        'description',
    ],
]);
