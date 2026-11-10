<?php

use Cake\Core\Configure;
use Croogo\Core\Croogo;
use Croogo\FileManager\Utility\StorageManager;

Configure::write('FileManager', [
    'editablePaths' => [
        WWW_ROOT . 'assets',
    ],
    'deletablePaths' => [
        WWW_ROOT . 'assets',
    ],
]);

StorageManager::config('LocalAttachment', [
    'description' => 'Local Attachment',
    'adapterOptions' => [WWW_ROOT . 'assets', true],
    'adapterClass' => '\League\Flysystem\Adapter\Local',
    'class' => '\League\Flysystem\Filesystem',
]);
StorageManager::config('LegacyLocalAttachment', [
    'description' => 'Local Attachment (Legacy)',
    'adapterOptions' => [WWW_ROOT . 'uploads', true],
    'adapterClass' => '\League\Flysystem\Adapter\Local',
    'class' => '\League\Flysystem\Filesystem',
]);

Croogo::hookHelper('*', 'Croogo/FileManager.AssetsFilter');
