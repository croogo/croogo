<?php

use Cake\Core\Configure;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Croogo;

CroogoCache::config('contacts_view', array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['contacts']]
));

Croogo::mergeConfig('Translate.models.Contact', [
    'fields' => [
        'title' => 'titleTranslation',
        'body' => 'bodyTranslation',
    ],
    'translateModel' => 'Contacts.Contact',
]);
