<?php

namespace Croogo\Menus\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'menus', array(
	'icon' => 'sitemap',
	'title' => __d('croogo', 'Menus'),
	'url' => array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Menus',
		'controller' => 'Menus',
		'action' => 'index',
	),
	'weight' => 20,
	'children' => array(
		'menus' => array(
			'title' => __d('croogo', 'Menus'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Menus',
				'controller' => 'Menus',
				'action' => 'index',
			),
			'weight' => 10,
		),
	),
));
