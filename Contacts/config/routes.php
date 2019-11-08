<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::plugin('Croogo/Contacts', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);
        $route->applyMiddleware('csrf');

        $route->scope('/contacts', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    Router::build($route, '/contact', ['controller' => 'Contacts', 'action' => 'view', 'contact']);
    Router::build($route, '/contact/*', ['controller' => 'Contacts', 'action' => 'view']);
});
