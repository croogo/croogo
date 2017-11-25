<?php

use Cake\Core\Configure;
use Cake\Cache\Cache;
use Croogo\Core\Croogo;

$cacheConfig = array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['taxonomy']]
);
Cache::config('croogo_types', $cacheConfig);
Cache::config('croogo_vocabularies', $cacheConfig);

Croogo::hookComponent('*', 'Croogo/Taxonomy.Taxonomies');

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
