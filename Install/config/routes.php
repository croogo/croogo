<?php

use Cake\Routing\Router;

$routes->connect('/*', []);

$routes->plugin('Croogo/Install', ['path' => '/install'], function ($route) {
    $route->fallbacks();
});
