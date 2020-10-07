<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Acl', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->scope('/acl', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });
});
