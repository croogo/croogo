<?php

// Basic
CroogoRouter::connect('/', array(
	'plugin' => 'contents', 'controller' => 'nodes', 'action' => 'promoted'
));

CroogoRouter::connect('/promoted/*', array(
	'plugin' => 'contents', 'controller' => 'nodes', 'action' => 'promoted'
));

CroogoRouter::connect('/search/*', array(
	'plugin' => 'contents', 'controller' => 'nodes', 'action' => 'search'
));

// Content types
CroogoRouter::contentType('blog');
CroogoRouter::contentType('node');
if (Configure::read('Install.installed')) {
	CroogoRouter::routableContentTypes();
}

// Page
CroogoRouter::connect('/about', array(
	'plugin' => 'contents', 'controller' => 'nodes', 'action' => 'view',
	'type' => 'page', 'slug' => 'about'
	));
CroogoRouter::connect('/page/:slug', array(
	'plugin' => 'contents', 'controller' => 'nodes', 'action' => 'view',
	'type' => 'page'
));
