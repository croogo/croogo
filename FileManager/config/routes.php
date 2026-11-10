<?php
/** @var \Cake\Routing\RouteBuilder $routes */
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

$routes->plugin('Croogo/FileManager', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);
        $route->applyMiddleware('csrf');

        $route->scope('/file-manager', [], function (RouteBuilder $route) {
            $route->fallbacks('Croogo/Core.InflectedRoute');
        });
    });
});
