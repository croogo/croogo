<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::plugin('Croogo/Example', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->setExtensions(['json']);
        $routeBuilder->applyMiddleware('csrf');

        $routeBuilder->connect('/route/here', [
            'plugin' => 'Croogo/Example',
            'controller' => 'Example',
            'action' => 'index',
        ]);

        $routeBuilder->connect('/example/:action/*', [
            'prefix' => 'admin',
            'plugin' => 'Croogo/Example',
            'controller' => 'Example',
        ]);
    });

    $routeBuilder->connect('/example/:action/*', [
        'plugin' => 'Croogo/Example',
        'controller' => 'Example',
    ]);

    $routeBuilder->fallbacks();
});
