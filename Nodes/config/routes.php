<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Routing\Router;

$routes->plugin('Croogo/Nodes', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->scope('/nodes', [], function (RouteBuilder $route) {
            $route->connect('/', ['controller' => 'Nodes']);
            $route->fallbacks();
        });
    });

    $route->prefix('Api', function (RouteBuilder $route) {
        $route->prefix('V10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->connect('/nodes', ['controller' => 'Nodes', 'action' => 'index']);
            $route->resources('Nodes', [
                'only' => [
                    'index', 'view', 'lookup',
                ],
                'map' => [
                    'lookup' => [
                        'action' => 'lookup',
                        'method' => 'GET',
                    ],
                ],
            ]);
        });
    });

    $route->setExtensions(['rss']);

    $route->connect('/', ['controller' => 'Nodes', 'action' => 'promoted']);
    $route->connect('/feed', ['controller' => 'Nodes', 'action' => 'feed']);
    $route->connect('/promoted', ['controller' => 'Nodes', 'action' => 'feed']);
    $route->connect('/search', ['controller' => 'Nodes', 'action' => 'search']);

    // Content types
    Router::routableContentTypes($route);
});
