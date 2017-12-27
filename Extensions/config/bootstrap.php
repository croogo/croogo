<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Croogo\Extensions\CroogoPlugin;

if (!Plugin::loaded('Migrations')) {
    Plugin::load('Migrations', ['autoload' => true, 'classBase' => false]);
}
if (!Plugin::loaded('Croogo/Settings')) {
    Plugin::load('Croogo/Settings', ['bootstrap' => true, 'routes' => true]);
}
if (!Plugin::loaded('Search')) {
    Plugin::load('Search', ['autoload' => true, 'classBase' => false]);
}

class_alias('Croogo\Core\Plugin', 'Croogo\Extensions\CroogoPlugin');
