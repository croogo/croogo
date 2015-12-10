<?php

/**
 * Dashboard URL
 */
use Cake\Core\Configure;
use Croogo\Dashboards\Configure\DashboardsConfigReader;

Configure::write('Croogo.dashboardUrl', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Dashboards',
    'controller' => 'DashboardsDashboards',
    'action' => 'dashboard',
]);

Configure::config('dashboards', new DashboardsConfigReader());
