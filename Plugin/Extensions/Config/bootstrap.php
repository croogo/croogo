<?php

CroogoNav::add('extensions', array(
	'title' => 'Extensions',
	'url' => array(
		'plugin' => 'extensions',
		'controller' => 'extensions_plugins',
		'action' => 'index',
	),
	'weight' => 35,
	'children' => array(
		'themes' => array(
			'title' => __('Themes'),
			'url' => array(
				'plugin' => 'extensions',
				'controller' => 'extensions_themes',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'locales' => array(
			'title' => __('Locales'),
			'url' => array(
				'plugin' => 'extensions',
				'controller' => 'extensions_locales',
				'action' => 'index',
			),
			'weight' => 20,
		),
		'plugins' => array(
			'title' => __('Plugins'),
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
