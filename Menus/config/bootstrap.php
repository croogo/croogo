<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Core\Croogo;

Cache::setConfig('croogo_menus', array_merge(
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
