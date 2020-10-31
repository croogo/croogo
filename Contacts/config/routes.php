<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Contacts', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/contacts', [], function (RouteBuilder $route) {
            $route->connect('/', ['controller' => 'Contacts']);
            $route->fallbacks();
        });
    });

    $route->prefix('Api', function (RouteBuilder $route) {
        $route->prefix('V10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->connect('/contacts', ['controller' => 'Contacts', 'action' => 'index']);
            $route->resources('Contacts', [
                'only' => ['index', 'view']
            ]);

            $route->connect('/Messages', ['controller' => 'Messages', 'action' => 'index']);
            $route->resources('Messages', [
                'only' => ['index', 'view']
            ]);
        });
    });

    $route->connect('/contact', ['controller' => 'Contacts', 'action' => 'view', 'contact']);
    $route->connect('/contact/*', ['controller' => 'Contacts', 'action' => 'view']);
});
