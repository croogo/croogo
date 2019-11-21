<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Croogo/Taxonomy', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);
        $route->applyMiddleware('csrf');

        $route->scope('/taxonomy', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });
});

Router::plugin('Croogo/Taxonomy', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('api', function (RouteBuilder $route) {
        $route->prefix('v10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->resources('Taxonomies');
            $route->resources('Terms');
            $route->resources('Types');
            $route->resources('Vocabularies');
        });
    });
});
