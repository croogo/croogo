<?php

use Cake\Core\Configure;
use Cake\Routing\RouteBuilder;
use Croogo\Core\Utility\StringConverter;

$routes->prefix('Admin', function (RouteBuilder $routeBuilder) {
    $dashboardUrl = Configure::read('Site.dashboard_url');
    if (!$dashboardUrl) {
        return;
    }

    if (is_string($dashboardUrl)) {
        $converter = new StringConverter();
        $dashboardUrl = $converter->linkStringToArray($dashboardUrl);
    }

    $routeBuilder->connect('/', $dashboardUrl);
});

$routes->plugin('Croogo/Core', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('Admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->setExtensions(['json']);

        $routeBuilder->connect('/link-chooser/*', ['controller' => 'LinkChooser', 'action' => 'linkChooser']);
    });
});
