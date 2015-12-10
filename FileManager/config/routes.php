<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/filemanager/:controller/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/FileManager',
]);

CroogoRouter::connect('/filemanager/:controller/:action/*', [
    'plugin' => 'Croogo/FileManager',
]);
