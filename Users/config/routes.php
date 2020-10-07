<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->scope('/users', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    $route->prefix('Api', function (RouteBuilder $route) {
        $route->prefix('V10', ['path' => '/v1.0'], function (RouteBuilder $route) {
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

    $route->connect('/register', ['controller' => 'Users', 'action' => 'add']);
    $route->connect('/user/:username', ['controller' => 'Users', 'action' => 'view'], ['pass' => ['username']]);

    $route->connect('/users', ['controller' => 'Users', 'action' => 'index']);
    $route->connect('/users/:action/*', ['controller' => 'Users']);
});
