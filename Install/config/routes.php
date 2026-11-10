<?php
/** @var \Cake\Routing\RouteBuilder $routes */
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

$routes->connect('/*', []);

$routes->plugin('Croogo/Install', ['path' => '/install'], function ($route) {
    $route->applyMiddleware('csrf');
    $route->fallbacks('Croogo/Core.InflectedRoute');
});
