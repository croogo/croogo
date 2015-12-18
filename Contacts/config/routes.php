<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Contacts', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/contacts/contacts/:action/*', ['controller' => 'Contacts']);
        $routeBuilder->connect('/contacts/messages/:action/*', ['controller' => 'Messages']);
    });

    $routeBuilder->connect('/contact/*', ['controller' => 'Contacts', 'action' => 'view']);
});
