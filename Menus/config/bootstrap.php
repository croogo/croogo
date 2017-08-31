<?php

use Cake\Core\Configure;
use Cake\Cache\Cache;
use Croogo\Core\Croogo;

Cache::config('croogo_menus', array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['menus']]
));

Croogo::hookComponent('*', 'Croogo/Menus.Menu');

Croogo::hookHelper('*', 'Croogo/Menus.Menus');

Croogo::translateModel('Croogo/Menus.Links', [
    'fields' => [
        'title',
        'description',
    ],
]);
