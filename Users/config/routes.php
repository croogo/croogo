<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

$routes->plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/users', [], function (RouteBuilder $route) {
            $route->connect('/users', ['prefix' => 'Admin', 'controller' => 'Users', 'action' => 'index']);
            $route->connect('/users/:action/*', ['prefix' => 'Admin', 'controller' => 'Users']);
            $route->connect('/roles', ['prefix' => 'Admin', 'controller' => 'Roles', 'action' => 'index']);
            $route->connect('/roles/:action/*', ['prefix' => 'Admin', 'controller' => 'Roles']);
        });
    });

    $route->connect('/register', ['controller' => 'Users', 'action' => 'add']);
    $route->connect('/user/:username', ['controller' => 'Users', 'action' => 'view'], ['pass' => ['username']]);

    $route->connect('/users', ['controller' => 'Users', 'action' => 'index']);
    $route->connect('/users/:action/*', ['controller' => 'Users']);
});

$routes->plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $route) {
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
