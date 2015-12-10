<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/dashboards', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Dashboards',
    'controller' => 'DashboardsDashboards',
    'action' => 'dashboard',
]);

CroogoRouter::connect('/admin/dashboards/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Dashboards',
    'controller' => 'DashboardsDashboards',
], [
    'action' => '[a-zA-Z0-9_-]+',
]);
