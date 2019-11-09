<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::connect('/*', []);

Router::plugin('Croogo/Install', ['path' => '/install'], function ($route) {
    $route->applyMiddleware('csrf');
    $route->fallbacks();
});
