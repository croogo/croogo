<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Blocks', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/blocks', [], function (RouteBuilder $route) {
            $route->connect('/blocks', ['prefix' => 'Admin', 'controller' => 'Blocks', 'action' => 'index']);
            $route->connect('/blocks/:action/*', ['prefix' => 'Admin', 'controller' => 'Blocks']);
            $route->connect('/regions', ['prefix' => 'Admin', 'controller' => 'Regions', 'action' => 'index']);
            $route->connect('/regions/:action/*', ['prefix' => 'Admin', 'controller' => 'Regions']);
        });
    });
});

$routes->plugin('Croogo/Blocks', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Api', function (RouteBuilder $route) {
        $route->prefix('V10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->resources('Blocks');
        });
    });
});
