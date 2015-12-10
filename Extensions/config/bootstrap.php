<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Croogo\Extensions\CroogoPlugin;

/**
 * Dashboard URL
 */
Configure::write('Croogo.dashboardUrl', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Extensions',
    'controller' => 'Dashboard',
    'action' => 'index',
]);

if (!Plugin::loaded('Migrations')) {
    Plugin::load('Migrations', ['autoload' => true, 'classBase' => false]);
}
if (!Plugin::loaded('Croogo/Settings')) {
    Plugin::load('Croogo/Settings', ['bootstrap' => true]);
}
if (!Plugin::loaded('Search')) {
    Plugin::load('Search', ['autoload' => true, 'classBase' => false]);
}
