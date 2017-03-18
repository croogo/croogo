<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

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

    $routeBuilder->connect('/example/route/here', [
        'plugin' => 'Croogo/Example',
        'controller' => 'example',
        'action' => 'index',
    ]);

    $routeBuilder->fallbacks();
});
