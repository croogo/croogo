<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/comments/:controller/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Comments',
]);
