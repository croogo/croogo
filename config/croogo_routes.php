<?php
	CroogoRouter::plugins();
	Router::parseExtensions('json', 'rss');

	// Installer
	if (!file_exists(CONFIGS.'settings.yml')) {
		CroogoRouter::connect('/', array('plugin' => 'install' ,'controller' => 'install'));
	}

	// Basic
	CroogoRouter::connect('/', array('controller' => 'nodes', 'action' => 'promoted'));
	CroogoRouter::connect('/promoted/*', array('controller' => 'nodes', 'action' => 'promoted'));
	CroogoRouter::connect('/admin', array('admin' => true, 'controller' => 'settings', 'action' => 'dashboard'));
	CroogoRouter::connect('/search/*', array('controller' => 'nodes', 'action' => 'search'));

	// Blog
	CroogoRouter::connect('/blog', array('controller' => 'nodes', 'action' => 'index', 'type' => 'blog'));
	CroogoRouter::connect('/blog/archives/*', array('controller' => 'nodes', 'action' => 'index', 'type' => 'blog'));
	CroogoRouter::connect('/blog/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'blog'));
	CroogoRouter::connect('/blog/term/:slug/*', array('controller' => 'nodes', 'action' => 'term', 'type' => 'blog'));

	// Node
	CroogoRouter::connect('/node', array('controller' => 'nodes', 'action' => 'index', 'type' => 'node'));
	CroogoRouter::connect('/node/archives/*', array('controller' => 'nodes', 'action' => 'index', 'type' => 'node'));
	CroogoRouter::connect('/node/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'node'));
	CroogoRouter::connect('/node/term/:slug/*', array('controller' => 'nodes', 'action' => 'term', 'type' => 'node'));

	// Page
	CroogoRouter::connect('/about', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page', 'slug' => 'about'));
	CroogoRouter::connect('/page/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page'));

	// Users
	CroogoRouter::connect('/register', array('controller' => 'users', 'action' => 'add'));
	CroogoRouter::connect('/user/:username', array('controller' => 'users', 'action' => 'view'), array('pass' => array('username')));

	// Contact
	CroogoRouter::connect('/contact', array('controller' => 'contacts', 'action' => 'view', 'contact'));
