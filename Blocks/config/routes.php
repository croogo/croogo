<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/blocks/blocks/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Blocks', 'controller' => 'Blocks',
]);

CroogoRouter::connect('/admin/blocks/regions/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Blocks', 'controller' => 'Regions',
]);
