<?php

use Cake\Core\Configure;
use Cake\Cache\Cache;
use Croogo\Core\Croogo;
use Croogo\Wysiwyg\Wysiwyg;

Cache::config('contacts_view', array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['contacts']]
));

Croogo::translateModel('Croogo/Contacts.Contacts', [
    'fields' => [
        'title',
        'body',
    ],
]);

// Configure Wysiwyg
Wysiwyg::setActions([
    'Croogo/Contacts.Admin/Contacts/add' => [
        [
            'elements' => 'body',
        ],
    ],
    'Croogo/Contacts.Admin/Contacts/edit' => [
        [
            'elements' => 'body',
        ],
    ],
]);
