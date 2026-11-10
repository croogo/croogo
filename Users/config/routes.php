<?php
/** @var \Cake\Routing\RouteBuilder $routes */
use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

$routes->plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);
        $route->applyMiddleware('csrf');

        $route->scope('/users', [], function (RouteBuilder $route) {
            $route->fallbacks('Croogo/Core.InflectedRoute');
        });
    });

    Router::build($route, '/', ['controller' => 'Users', 'action' => 'login']);

//    Router::build($route, '/register', ['controller' => 'Users', 'action' => 'add']);
//    Router::build($route, '/user/:username', ['controller' => 'Users', 'action' => 'view'], ['pass' => ['username']]);
//
//    Router::build($route, '/users', ['controller' => 'Users', 'action' => 'index']);
//    Router::build($route, '/users/:action/*', ['controller' => 'Users']);
});

$routes->plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Api', function (RouteBuilder $route) {
        $route->prefix('V10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->resources('Roles');
            $route->resources('Users', [
                'map' => [
                    'lookup' => [
                        'action' => 'lookup',
                        'method' => 'GET',
                    ]
                ],
            ]);

            $route->connect('/token', ['controller' => 'Users', 'action' => 'token', '_method' => 'POST']);
        });
    });
});
