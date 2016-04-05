<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Meta', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->fallbacks();
    });
});
