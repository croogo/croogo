<?php

/**
 * Dashboard URL
 */

use Cake\Core\Configure;
use Croogo\Dashboards\Configure\DashboardsConfigReader;
use Croogo\Core\Utility\StringConverter;

if (!Configure::check('Site.dashboard_url')) {
    $converter = new StringConverter();
    Configure::write('Site.dashboard_url', $converter->urlToLinkString([
        'prefix' => 'admin',
        'plugin' => 'Croogo/Dashboards',
        'controller' => 'Dashboards',
        'action' => 'dashboard',
    ]));
}

Configure::config('dashboards', new DashboardsConfigReader());
