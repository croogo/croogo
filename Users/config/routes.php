<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->extensions(['json']);

        $route->scope('/users', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    Router::build($route, '/register', ['controller' => 'Users', 'action' => 'add']);
    Router::build($route, '/user/:username', ['controller' => 'Users', 'action' => 'view'], ['pass' => ['username']]);

    Router::build($route, '/users', ['controller' => 'Users', 'action' => 'index']);
    Router::build($route, '/users/:action/*', ['controller' => 'Users']);
});

Router::plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('api', function (RouteBuilder $route) {
        $route->scope('/v1.0', ['prefix' => 'api/v10'], function (RouteBuilder $route) {
            $route->extensions(['json']);

            $route->scope('/users', [], function (RouteBuilder $route) {
                $route->fallbacks();
            });
        });
    });
});
