<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Dashboards', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/dashboards', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });
});
