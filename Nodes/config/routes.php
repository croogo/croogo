<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
use Croogo\Croogo\CroogoRouter;

CroogoRouter::mapResources('Nodes.Nodes', array(
	'prefix' => '/:api/:prefix/',
));

Router::connect('/:api/:prefix/nodes/lookup', array(
	'plugin' => 'Croogo/Nodes',
	'controller' => 'Nodes',
	'action' => 'lookup',
), array(
	'routeClass' => 'Croogo\Croogo\Routing\Route\ApiRoute',
));

// Basic
CroogoRouter::connect('/', array(
	'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'promoted'
));

CroogoRouter::connect('/promoted/*', array(
	'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'promoted'
));

CroogoRouter::connect('/search/*', array(
	'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'search'
));

// Content types
CroogoRouter::contentType('blog');
CroogoRouter::contentType('node');
if (Configure::read('Croogo.installed')) {
	CroogoRouter::routableContentTypes();
}

// Page
CroogoRouter::connect('/about', array(
	'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'view',
	'type' => 'page', 'slug' => 'about'
));
CroogoRouter::connect('/page/:slug', array(
	'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'view',
	'type' => 'page'
));

CroogoRouter::connect('/admin/nodes/:action/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
));

CroogoRouter::connect('/nodes/view/:type/:slug', array(
	'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes',
	'action' => 'view'
));

