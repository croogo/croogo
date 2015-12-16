<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Users', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/users', ['controller' => 'Users', 'action' => 'index']);
        $routeBuilder->connect('/users/:action/*', ['controller' => 'Users']);

        $routeBuilder->connect('/roles', ['controller' => 'Roles', 'action' => 'index']);
        $routeBuilder->connect('/roles/:action/*', ['controller' => 'Roles']);
    });

    $routeBuilder->connect('/register', ['controller' => 'Users', 'action' => 'add']);
    $routeBuilder->connect('/user/:username', ['controller' => 'Users', 'action' => 'view'], ['pass' => ['username']]);

    $routeBuilder->connect('/users', ['controller' => 'Users', 'action' => 'index']);
    $routeBuilder->connect('/users/:action/*', ['controller' => 'Users']);

    $routeBuilder->connect('/roles', ['controller' => 'Roles', 'action' => 'index']);
    $routeBuilder->connect('/roles/:action/*', ['controller' => 'Roles']);
});
