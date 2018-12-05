<?php

use Cake\Core\Configure;
use Croogo\Core\Croogo;
use Croogo\Wysiwyg\Wysiwyg;
use Croogo\FileManager\Utility\StorageManager;

Configure::write('Wysiwyg.attachmentBrowseUrl', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/FileManager',
    'controller' => 'Attachments',
    'action' => 'browse',
]);

Wysiwyg::setActions([
    'Croogo/FileManager.Admin/Attachments/browse' => [],
]);

Configure::write('FileManager', [
    'editablePaths' => [
        APP,
    ],
    'deletablePaths' => [
        APP . 'View' . DS . 'Themed' . DS,
        WWW_ROOT,
    ],
]);

StorageManager::config('LocalAttachment', array(
    'description' => 'Local Attachment',
    'adapterOptions' => array(WWW_ROOT . 'assets', true),
    'adapterClass' => '\League\Flysystem\Adapter\Local',
    'class' => '\League\Flysystem\Filesystem',
));
StorageManager::config('LegacyLocalAttachment', array(
    'description' => 'Local Attachment (Legacy)',
    'adapterOptions' => array(WWW_ROOT . 'uploads', true),
    'adapterClass' => '\League\Flysystem\Adapter\Local',
    'class' => '\League\Flysystem\Filesystem',
));

// TODO: make this configurable via backend
$actions = [
    'Admin/Blocks/edit',
    'Admin/Contacts/edit',
    'Admin/Nodes/edit',
    'Admin/Types/edit',
];
$tabTitle = __d('assets', 'Assets');
foreach ($actions as $action):
    list($controller, ) = explode('/', $action);
    Croogo::hookAdminTab($action, $tabTitle, 'Croogo/FileManager.admin/asset_list');
    Croogo::hookHelper($controller, 'Croogo/FileManager.AssetsAdmin');
endforeach;

// TODO: make this configurable via backend
$models = [
    'Croogo/Blocks.Blocks',
    'Croogo/Contacts.Contacts',
    'Croogo/Nodes.Nodes',
    'Croogo/Taxonomy.Types',
];
foreach ($models as $model) {
    Croogo::hookBehavior($model, 'Croogo/FileManager.LinkedAssets', ['priority' => 9]);
}

Croogo::hookHelper('*', 'Croogo/FileManager.AssetsFilter');
