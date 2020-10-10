<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Blocks', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->scope('/blocks', [], function (RouteBuilder $route) {
            $route->connect('/', ['controller' => 'Blocks']);
            $route->fallbacks();
        });
    });

    $route->prefix('Api', function (RouteBuilder $route) {
        $route->prefix('V10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->connect('/blocks', ['controller' => 'Blocks', 'action' => 'index']);
            $route->resources('Blocks', [
                'only' => ['index', 'view']
            ]);

            $route->connect('/regions', ['controller' => 'Regions', 'action' => 'index']);
            $route->resources('Regions', [
                'only' => ['index', 'view']
            ]);
        });
    });
});
