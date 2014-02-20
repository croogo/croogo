<?php

CroogoNav::add('sidebar', 'menus', array(
	'icon' => array('sitemap', 'large'),
	'title' => __d('croogo', 'Menus'),
	'url' => array(
		'plugin' => 'menus',
		'admin' => true,
		'controller' => 'menus',
		'action' => 'index',
	),
	'weight' => 20,
	'children' => array(
		'menus' => array(
			'title' => __d('croogo', 'Menus'),
			'url' => array(
				'plugin' => 'menus',
				'admin' => true,
				'controller' => 'menus',
				'action' => 'index',
			),
			'weight' => 10,
		),
	),
));
