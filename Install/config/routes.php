<?php

use Cake\Routing\Router;

Router::connect('/*', []);

Router::plugin('Croogo/Install', ['path' => '/install'], function ($route) {
    $route->fallbacks();
});
