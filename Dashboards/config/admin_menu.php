<?php

use Croogo\Core\Nav;

Nav::add('sidebar', 'dashboard', array(
	'icon' => 'home',
	'title' => __d('croogo', 'Dashboard'),
	'url' => '/admin',
	'weight' => 0,
));

Nav::add('sidebar', 'settings.children.dashboard', array(
	'title' => __d('croogo', 'Dashboard'),
	'url' => array(
		'plugin' => 'Croogo/Dashboards',
		'controller' => 'DashboardsDashboards',
		'action' => 'index',
	),
));
