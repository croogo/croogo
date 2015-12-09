<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/settings/:controller/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Settings',
]);
