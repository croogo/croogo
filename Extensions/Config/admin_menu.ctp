<?php

CroogoNav::add('sidebar', 'extensions', array(
	'icon' => array('magic', 'large'),
	'title' => __d('croogo', 'Extensions'),
	'url' => array(
		'plugin' => 'extensions',
		'controller' => 'extensions_plugins',
		'action' => 'index',
	),
	'weight' => 35,
	'children' => array(
		'themes' => array(
			'title' => __d('croogo', 'Themes'),
			'url' => array(
				'plugin' => 'extensions',
				'controller' => 'extensions_themes',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'locales' => array(
			'title' => __d('croogo', 'Locales'),
			'url' => array(
				'plugin' => 'extensions',
				'controller' => 'extensions_locales',
				'action' => 'index',
			),
			'weight' => 20,
		),
		'plugins' => array(
			'title' => __d('croogo', 'Plugins'),
			'url' => array(
				'plugin' => 'extensions',
				'controller' => 'extensions_plugins',
				'action' => 'index',
			),
			'htmlAttributes' => array(
				'class' => 'separator',
			),
			'weight' => 30,
		),
	),
));
