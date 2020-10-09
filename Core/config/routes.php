<?php

use Cake\Core\Configure;
use Cake\Routing\RouteBuilder;
use Croogo\Core\Utility\StringConverter;

$routes->prefix('Admin', function (RouteBuilder $route) {
    $dashboardUrl = Configure::read('Site.dashboard_url');
    if (!$dashboardUrl) {
        return;
    }

    if (is_string($dashboardUrl)) {
        $converter = new StringConverter();
        $dashboardUrl = $converter->linkStringToArray($dashboardUrl);
    }

    $route->connect('/', $dashboardUrl);
});

$routes->plugin('Croogo/Core', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->connect('/link-chooser/*', ['controller' => 'LinkChooser', 'action' => 'linkChooser']);
        $route->fallbacks();
    });

    $route->connect('/.well-known/:controller/*', [
        'action' => 'index',
        '_ext' => 'json',
    ], [
        'controller' => '(jwks)',
    ]);

});
