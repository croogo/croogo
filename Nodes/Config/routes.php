<?php

CroogoRouter::mapResources('Nodes.Nodes', array(
	'prefix' => '/:api/:prefix/',
));

Router::connect('/:api/:prefix/nodes/lookup', array(
	'plugin' => 'nodes',
	'controller' => 'nodes',
	'action' => 'lookup',
), array(
	'routeClass' => 'ApiRoute',
));

// Basic
CroogoRouter::connect('/', array(
	'plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'promoted'
));

CroogoRouter::connect('/promoted/*', array(
	'plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'promoted'
));

CroogoRouter::connect('/search/*', array(
	'plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'search'
));

// Content types
CroogoRouter::contentType('blog');
CroogoRouter::contentType('node');
if (Configure::read('Croogo.installed')) {
	CroogoRouter::routableContentTypes();
}

// Page
CroogoRouter::connect('/about', array(
	'plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'view',
	'type' => 'page', 'slug' => 'about'
));
CroogoRouter::connect('/page/:slug', array(
	'plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'view',
	'type' => 'page'
));
