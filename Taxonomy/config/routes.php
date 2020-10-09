<?php

use Cake\Routing\RouteBuilder;

$routes->plugin('Croogo/Taxonomy', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('Admin', function (RouteBuilder $route) {
        $route->setExtensions(['json']);

        $route->scope('/taxonomy', [], function (RouteBuilder $route) {
            $route->fallbacks();
        });
    });

    $route->prefix('Api', function (RouteBuilder $route) {
        $route->prefix('V10', ['path' => '/v1.0'], function (RouteBuilder $route) {
            $route->setExtensions(['json']);

            $route->resources('Taxonomies', [
                'only' => ['index', 'view'],
            ]);

            $route->resources('Terms', [
                'only' => ['index', 'view'],
            ]);

            $route->connect('/types', ['controller' => 'Types', 'action' => 'index']);
            $route->resources('Types', [
                'only' => ['index', 'view'],
            ]);

            $route->resources('Vocabularies', [
                'only' => ['index', 'view'],
            ]);
        });
    });
});
