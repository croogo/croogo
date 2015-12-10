<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Croogo\Core\CroogoRouter;

Router::plugin('Croogo/Users', function (RouteBuilder $routeBuilder) {
    $routeBuilder->fallbacks();
});

CroogoRouter::mapResources('Users.Users', [
    'prefix' => '/:api/:prefix/',
]);

Router::connect('/:api/:prefix/users/lookup', [
    'plugin' => 'users',
    'controller' => 'users',
    'action' => 'lookup',
], [
    'routeClass' => 'Croogo\Core\Routing\Route\ApiRoute',
]);

// Users
CroogoRouter::connect('/register', ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'add']);

CroogoRouter::connect('/user/:username', [
    'plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'view'], ['pass' => ['username']
    ]);

    CroogoRouter::connect('/users/:controller/:action/*', [
    'plugin' => 'Croogo/Users'
    ]);

    CroogoRouter::connect('/admin/users/:controller/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Users'
    ]);
