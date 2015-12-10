<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/acl/:controller/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Acl'
]);
