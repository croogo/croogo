<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/FileManager', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/filemanager/:controller/:action/*', [ ]);
    });

    $routeBuilder->connect('/filemanager/:controller/:action/*', [ ]);
});
