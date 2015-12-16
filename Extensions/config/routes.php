<?php

use Cake\Core\Configure;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Extensions', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/', Configure::read('Croogo.dashboardUrl'));
        $routeBuilder->connect('/extensions/:controller/:action/*', [ ]);
    });
});
