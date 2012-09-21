<?php

Croogo::hookComponent('*', 'Menus.Menus');

Croogo::hookHelper('*', 'Menus.Menus');

CroogoNav::add('menus', array(
	'icon' => array('sitemap', 'large'),
	'title' => __('Menus'),
	'url' => array(
		'plugin' => 'menus',
		'admin' => true,
		'controller' => 'menus',
		'action' => 'index',
	),
	'weight' => 20,
	'children' => array(
		'menus' => array(
			'title' => __('Menus'),
			'url' => array(
				'plugin' => 'menus',
				'admin' => true,
				'controller' => 'menus',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'add_new' => array(
			'title' => __('Add new'),
			'url' => array(
				'plugin' => 'menus',
				'admin' => true,
				'controller' => 'menus',
				'action' => 'add',
			),
			'weight' => 20,
			'htmlAttributes' => array('class' => 'separator'),
		),
	),
));
