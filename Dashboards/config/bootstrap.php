<?php

/**
 * Dashboard URL
 */

use Cake\Core\Configure;
use Croogo\Dashboards\Configure\DashboardsConfigReader;

if (!Configure::check('Site.dashboardUrl')) {
    Configure::write('Site.dashboardUrl', [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Dashboards',
        'controller' => 'Dashboards',
        'action' => 'dashboard',
    ]);
}

Configure::config('dashboards', new DashboardsConfigReader());
