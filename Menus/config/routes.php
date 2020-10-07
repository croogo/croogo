<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Menus', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->scope('/menus', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    $route->prefix('Api', function (RouteBuilder $route) {
        $route->prefix('V10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->resources('Links');
            $route->resources('Menus');
        });
    });
});
