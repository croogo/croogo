<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/menus/menus/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Menus', 'controller' => 'Menus',
]);

CroogoRouter::connect('/admin/menus/links/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Menus', 'controller' => 'Links',
]);
