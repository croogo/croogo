<?php

CroogoNav::add('sidebar', 'menus', array(
	'icon' => 'sitemap',
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
