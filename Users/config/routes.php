<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->extensions(['json']);

        $route->scope('/users', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    $route->connect('/register', ['controller' => 'Users', 'action' => 'add']);
    $route->connect('/user/:username', ['controller' => 'Users', 'action' => 'view'], ['pass' => ['username']]);

    $route->connect('/users', ['controller' => 'Users', 'action' => 'index']);
    $route->connect('/users/:action/*', ['controller' => 'Users']);
});
