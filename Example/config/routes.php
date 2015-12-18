<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Taxonomy', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/example/admin/route/here', ['controller' => 'example', 'action' => 'index']);
    });

    $routeBuilder->connect('/example/route/here', ['controller' => 'example', 'action' => 'index']);
});
