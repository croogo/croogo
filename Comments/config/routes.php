<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Comments', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->extensions(['json']);

        $route->scope('/comments', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });

        $route->fallbacks();
    });
});
