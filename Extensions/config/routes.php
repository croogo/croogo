<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Extensions', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/extensions', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });
});
