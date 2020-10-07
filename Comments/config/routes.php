<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Comments', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->scope('/comments', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    $route->setExtensions(['rss']);

    $route->fallbacks();
});
