<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin', Configure::read('Croogo.dashboardUrl'));

CroogoRouter::connect('/admin/extensions/:controller/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Extensions'
]);
