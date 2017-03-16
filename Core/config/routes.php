<?php

use Cake\Core\Configure;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::prefix('admin', function (RouteBuilder $routeBuilder) {
    $dashboardUrl = Configure::read('Croogo.dashboardUrl');
    if (!$dashboardUrl) {
        return;
    }

    $routeBuilder->connect('/', $dashboardUrl);
});

Router::plugin('Croogo/Core', ['path' => '/'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->prefix('admin', function (RouteBuilder $routeBuilder) {
        $routeBuilder->extensions(['json']);

        $routeBuilder->connect('/link-chooser/*', ['controller' => 'LinkChooser', 'action' => 'linkChooser']);
    });
});
