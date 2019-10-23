<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);
        $route->applyMiddleware('csrf');

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
        $route->prefix('v10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->resources('Roles');
            $route->resources('Users', [
                'map' => [
                    'lookup' => [
                        'action' => 'lookup',
                        'method' => 'GET',
                    ],
                ],
            ]);
        });
    });
});
