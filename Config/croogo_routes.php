<?php
	CakePlugin::routes();
	Router::parseExtensions('json', 'rss');

	// Basic
	CroogoRouter::connect('/', array('controller' => 'nodes', 'action' => 'promoted'));
	CroogoRouter::connect('/promoted/*', array('controller' => 'nodes', 'action' => 'promoted'));
	CroogoRouter::connect('/admin', array('admin' => true, 'controller' => 'settings', 'action' => 'dashboard'));
	CroogoRouter::connect('/search/*', array('controller' => 'nodes', 'action' => 'search'));

	// Content types
	CroogoRouter::contentType('blog');
	CroogoRouter::contentType('node');
	if (Configure::read('Install.installed')) {
		CroogoRouter::routableContentTypes();
	}

	// Page
	CroogoRouter::connect('/about', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page', 'slug' => 'about'));
	CroogoRouter::connect('/page/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page'));

	// Users
	CroogoRouter::connect('/register', array('controller' => 'users', 'action' => 'add'));
	CroogoRouter::connect('/user/:username', array('controller' => 'users', 'action' => 'view'), array('pass' => array('username')));

	// Contact
	CroogoRouter::connect('/contact', array('controller' => 'contacts', 'action' => 'view', 'contact'));
