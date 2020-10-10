<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Settings', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->scope('/settings', [], function (RouteBuilder $route) {
            $route->connect('/', ['controller' => 'Settings', 'action' => 'prefix', 'Site']);
            $route->fallbacks();
        });
    });
});
