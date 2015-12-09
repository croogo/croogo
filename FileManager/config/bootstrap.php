<?php

use Cake\Core\Configure;
use Croogo\Core\Croogo;

Configure::write('Wysiwyg.attachmentBrowseUrl', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/FileManager',
    'controller' => 'Attachments',
    'action' => 'browse',
]);

Croogo::mergeConfig('Wysiwyg.actions', [
    'Croogo\FileManager\Controller\Admin\Attachments.browse',
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
