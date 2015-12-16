<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Dashboards', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/dashboards', ['controller' => 'DashboardsDashboards', 'action' => 'dashboard']);
        $routeBuilder->connect('/dashboards/:action/*', ['controller' => 'DashboardsDashboards']);
    });
});
