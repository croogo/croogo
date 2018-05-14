<?php

use Cake\Routing\RouteBuilder;
use Croogo\Core\Router;

Router::addUrlFilter(function ($params, $request = null) {
    if (!$request) {
        return $params;
    }

    if ($request->getParam('lang') && !isset($params['lang'])) {
        $params['lang'] = $request->getParam('lang');
    }

    return $params;
});

Router::plugin('Croogo/Translate', ['path' => '/'], function (RouteBuilder $route) {
    $route->prefix('admin', function (RouteBuilder $route) {
        $route->scope('/translate', [], function(RouteBuilder $route) {
            $route->fallbacks();
        });
    });
});
