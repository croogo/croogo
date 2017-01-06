<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::plugin('Croogo/Nodes', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/nodes/:action/*', ['controller' => 'Nodes']);
    });

    $routeBuilder->connect('/', ['controller' => 'Nodes', 'action' => 'promoted']);
    $routeBuilder->connect('/promoted/*', ['controller' => 'Nodes', 'action' => 'promoted']);
    $routeBuilder->connect('/search/*', ['controller' => 'Nodes', 'action' => 'search']);

    // Content types
    Router::routableContentTypes($routeBuilder);
    Router::contentType('_placeholder', $routeBuilder);
});
