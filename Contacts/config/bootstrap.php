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

// Configure Wysiwyg
Croogo::mergeConfig('Wysiwyg.actions', [
    'Croogo/Contacts.Admin/Contacts.add' => [
        [
            'elements' => 'body',
        ],
    ],
    'Croogo/Contacts.Admin/Contacts.edit' => [
        [
            'elements' => 'body',
        ],
    ],
]);
