<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Blocks', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/blocks/blocks/:action/*', ['controller' => 'Blocks']);
        $routeBuilder->connect('/blocks/regions/:action/*', ['controller' => 'Regions']);
    });
});
