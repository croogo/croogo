<?php

namespace Croogo\Menus\Config;
CroogoNav::add('sidebar', 'menus', array(
	'icon' => array('sitemap', 'large'),
	'title' => __d('croogo', 'Menus'),
	'url' => array(
		'admin' => true,
		'plugin' => 'menus',
		'controller' => 'menus',
		'action' => 'index',
	),
	'weight' => 20,
	'children' => array(
		'menus' => array(
			'title' => __d('croogo', 'Menus'),
			'url' => array(
				'admin' => true,
				'plugin' => 'menus',
				'controller' => 'menus',
				'action' => 'index',
			),
			'weight' => 10,
		),
	),
));
