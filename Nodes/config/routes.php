<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::plugin('Croogo/Nodes', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);
        $route->applyMiddleware('csrf');

        $route->scope('/nodes', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    $route->setExtensions(['rss']);

    Router::build($route, '/', ['controller' => 'Nodes', 'action' => 'promoted']);
    Router::build($route, '/feed', ['controller' => 'Nodes', 'action' => 'feed', '_ext' => 'rss']);
    Router::build($route, '/search', ['controller' => 'Nodes', 'action' => 'search']);

    // Content types
    Router::routableContentTypes($route);
    Router::build($route, '/:action/*', ['controller' => 'Nodes']);
});

Router::plugin('Croogo/Nodes', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('api', function (RouteBuilder $route) {
        $route->prefix('v10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->resources('Nodes', [
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
