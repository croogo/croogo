<?php
/** @var \Cake\Routing\RouteBuilder $routes */
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

$routes->plugin('Croogo/Dashboards', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);
        $route->applyMiddleware('csrf');

        $route->scope('/dashboards', [], function (RouteBuilder $route) {
            $route->fallbacks('Croogo/Core.InflectedRoute');
        });
    });
});
