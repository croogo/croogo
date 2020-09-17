<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/FileManager', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/file-manager', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });
});
