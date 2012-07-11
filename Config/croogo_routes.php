<?php
	CakePlugin::routes();
	Router::parseExtensions('json', 'rss');

	// Basic
	CroogoRouter::connect('/', array('controller' => 'nodes', 'action' => 'promoted'));
	CroogoRouter::connect('/promoted/*', array('controller' => 'nodes', 'action' => 'promoted'));
	CroogoRouter::connect('/admin', array('admin' => true, 'controller' => 'settings', 'action' => 'dashboard'));
	CroogoRouter::connect('/search/*', array('controller' => 'nodes', 'action' => 'search'));

	// Page
	CroogoRouter::connect('/about', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page', 'slug' => 'about'));
	CroogoRouter::connect('/page/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page'));

	// Contact
	CroogoRouter::connect('/contact', array('controller' => 'contacts', 'action' => 'view', 'contact'));
