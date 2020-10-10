<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Contacts', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/contacts', [], function (RouteBuilder $route) {
            $route->connect('/', ['controller' => 'Contacts']);
            $route->fallbacks();
        });
    });

    $route->connect('/contact', ['controller' => 'Contacts', 'action' => 'view', 'contact']);
    $route->connect('/contact/*', ['controller' => 'Contacts', 'action' => 'view']);
});
