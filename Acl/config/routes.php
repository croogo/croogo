<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Acl', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/acl', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });
});
