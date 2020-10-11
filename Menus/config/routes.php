<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Menus', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->scope('/menus', [], function (RouteBuilder $route) {
            $route->connect('/', ['controller' => 'Menus']);
            $route->fallbacks();
        });
    });

    $route->prefix('Api', function (RouteBuilder $route) {
        $route->prefix('V10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->connect('/links', ['controller' => 'Links', 'action' => 'index']);
            $route->resources('Links', [
                'only' => [
                    'index', 'view',
                ],
            ]);

            $route->connect('/menus', ['controller' => 'Menus', 'action' => 'index']);
            $route->resources('Menus', [
                'only' => [
                    'index', 'view',
                ],
            ]);
        });
    });
});
