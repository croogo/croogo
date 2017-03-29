<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::plugin('Croogo/Example', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/example/admin/route/here', [
            'plugin' => 'Croogo/Example',
            'controller' => 'Example',
            'action' => 'index',
        ]);

        $routeBuilder->fallbacks();
    });

    Router::build($routeBuilder, '/example/route/here', [
        'plugin' => 'Croogo/Example',
        'controller' => 'example',
        'action' => 'index',
    ]);

    $routeBuilder->fallbacks();
});
