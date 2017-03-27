<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::plugin('Croogo/Nodes', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->extensions(['json']);

        $route->scope('/nodes', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    $route->extensions(['rss']);

    $route->connect('/', ['controller' => 'Nodes', 'action' => 'promoted']);
    $route->connect('/promoted/*', ['controller' => 'Nodes', 'action' => 'promoted']);
    $route->connect('/search/*', ['controller' => 'Nodes', 'action' => 'search']);

    // Content types
    Router::routableContentTypes($route);
});
