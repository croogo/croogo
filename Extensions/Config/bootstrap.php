<?php

/**
 * Dashboard URL
 */
Configure::write('Croogo.dashboardUrl', array(
	'admin' => true,
	'plugin' => 'extensions',
	'controller' => 'extensions_dashboard',
	'action' => 'index',
));

if (!CakePlugin::loaded('Migrations')) {
	CakePlugin::load('Migrations');
}
if (!CakePlugin::loaded('Settings')) {
	CakePlugin::load('Settings');
}
if (!CakePlugin::loaded('Search')) {
	CakePlugin::load('Search');
}
