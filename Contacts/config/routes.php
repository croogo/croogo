<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Contacts', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->extensions(['json']);

        $route->scope('/contacts', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    $route->connect('/contact', ['controller' => 'Contacts', 'action' => 'view', 'contact']);
    $route->connect('/contact/*', ['controller' => 'Contacts', 'action' => 'view']);
});
