<?php

use Cake\Core\Configure;
use Cake\Cache\Cache;
use Croogo\Core\Croogo;

Configure::write(
    'DebugKit.panels',
    array_merge((array)Configure::read('DebugKit.panels'), [
        'Croogo/Settings.Settings',
    ])
);

Croogo::hookComponent('*', [
    'SettingsComponent' => [
        'className' => 'Croogo/Settings.Settings'
    ]
]);
