<?php

use Cake\Routing\Router;

Router::scope('/install', ['plugin' => 'Croogo/Install', 'controller' => 'Install'], function (\Cake\Routing\RouteBuilder $routeBuilder) {
    $routeBuilder->connect('/', ['action' => 'index']);
    $routeBuilder->connect('/:action');
});
