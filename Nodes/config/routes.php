<?php

use Cake\Core\Configure;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Croogo\Core\CroogoRouter;

Router::plugin('Croogo/Nodes', function (RouteBuilder $routeBuilder) {
    $routeBuilder->fallbacks();
});

CroogoRouter::mapResources('Nodes.Nodes', [
    'prefix' => '/:api/:prefix/',
]);

Router::connect('/:api/:prefix/nodes/lookup', [
    'plugin' => 'Croogo/Nodes',
    'controller' => 'Nodes',
    'action' => 'lookup',
], [
    'routeClass' => 'Croogo\Core\Routing\Route\ApiRoute',
]);

// Basic
CroogoRouter::connect('/', [
    'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'promoted'
]);

CroogoRouter::connect('/promoted/*', [
    'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'promoted'
]);

CroogoRouter::connect('/search/*', [
    'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'search'
]);

// Content types
CroogoRouter::contentType('blog');
CroogoRouter::contentType('node');
if (Configure::read('Croogo.installed')) {
    CroogoRouter::routableContentTypes();
}

// Page
CroogoRouter::connect('/about', [
    'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'view',
    'type' => 'page', 'slug' => 'about'
]);
CroogoRouter::connect('/page/:slug', [
    'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'view',
    'type' => 'page'
]);

CroogoRouter::connect('/admin/nodes/:action/*', [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
]);

CroogoRouter::connect('/nodes/view/:type/:slug', [
    'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
    'action' => 'view'
]);
