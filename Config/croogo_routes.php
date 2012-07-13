<?php
	CakePlugin::routes();
	Router::parseExtensions('json', 'rss');

	CroogoRouter::connect('/admin', array(
		'admin' => true, 'controller' => 'settings', 'action' => 'dashboard'
	));
