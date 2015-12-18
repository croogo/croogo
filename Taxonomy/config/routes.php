<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Taxonomy', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/terms/:action/*', ['controller' => 'Terms']);
        $routeBuilder->connect('/types/:action/*', ['controller' => 'Types']);
        $routeBuilder->connect('/vocabularies/:action/*', ['controller' => 'Vocabularies']);
    });
});
