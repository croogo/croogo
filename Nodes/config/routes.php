<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Croogo\Core\CroogoRouter;

Router::plugin('Croogo/Nodes', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/nodes/:action/*', ['controller' => 'Nodes']);
    });

    $routeBuilder->connect('/', ['controller' => 'Nodes', 'action' => 'promoted']);
    $routeBuilder->connect('/promoted/*', ['controller' => 'Nodes', 'action' => 'promoted']);
    $routeBuilder->connect('/search/*', ['controller' => 'Nodes', 'action' => 'search']);

    // Content types
    CroogoRouter::routableContentTypes($routeBuilder);
});
