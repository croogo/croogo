<?php

use Cake\Core\Configure;
use Croogo\Core\Croogo;
use Croogo\Wysiwyg\Wysiwyg;

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
