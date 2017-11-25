<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::plugin('Croogo/Comments', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->extensions(['json']);

        $route->scope('/comments', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });

    });

    $route->extensions(['rss']);

    $route->scope('/comments', [], function (RouteBuilder $route) {
        Router::build($route, '/', ['controller' => 'Comments']);

        $route->fallbacks();
    });
});
