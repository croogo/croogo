<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Meta', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/meta', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });
});

$routes->plugin('Croogo/Meta', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('api', function (RouteBuilder $route) {
        $route->prefix('v10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->resources('Meta');
        });
    });
});
