<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::connect('/', []);

Router::scope('/install', ['plugin' => 'Croogo/Install', 'controller' => 'Install'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->applyMiddleware('csrf');
    $routeBuilder->connect('/', ['action' => 'index']);
    $routeBuilder->connect('/:action');
});
