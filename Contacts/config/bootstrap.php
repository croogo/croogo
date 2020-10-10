<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Core\Croogo;
use Croogo\Wysiwyg\Wysiwyg;

Cache::setConfig('contacts_view', array_merge(
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
            'elements' => '#body',
        ],
    ],
    'Croogo/Contacts.Admin/Contacts/edit' => [
        [
            'elements' => '#body',
        ],
    ],
]);
