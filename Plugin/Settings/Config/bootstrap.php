<?php

Croogo::hookComponent('*', 'Settings.Settings');

CroogoNav::add('settings', array(
	'icon' => array('cog', 'large'),
	'title' => __('Settings'),
	'url' => array(
		'admin' => true,
		'plugin' => 'settings',
		'controller' => 'settings',
		'action' => 'prefix',
		'Site',
	),
	'weight' => 60,
	'children' => array(
		'site' => array(
			'title' => __('Site'),
			'url' => array(
				'admin' => true,
				'plugin' => 'settings',
				'controller' => 'settings',
				'action' => 'prefix',
				'Site',
			),
			'weight' => 10,
		),

		'meta' => array(
			'title' => __('Meta'),
			'url' => array(
				'admin' => true,
				'plugin' => 'settings',
				'controller' => 'settings',
				'action' => 'prefix',
				'Meta',
			),
			'weight' => 20,
		),

		'reading' => array(
			'title' => __('Reading'),
			'url' => array(
				'admin' => true,
				'plugin' => 'settings',
				'controller' => 'settings',
				'action' => 'prefix',
				'Reading',
			),
			'weight' => 30,
		),

		'writing' => array(
			'title' => __('Writing'),
			'url' => array(
				'admin' => true,
				'plugin' => 'settings',
				'controller' => 'settings',
				'action' => 'prefix',
				'Writing',
			),
			'weight' => 40,
		),

		'comment' => array(
			'title' => __('Comment'),
			'url' => array(
				'admin' => true,
				'plugin' => 'settings',
				'controller' => 'settings',
				'action' => 'prefix',
				'Comment',
			),
			'weight' => 50,
		),

		'service' => array(
			'title' => __('Service'),
			'url' => array(
				'admin' => true,
				'plugin' => 'settings',
				'controller' => 'settings',
				'action' => 'prefix',
				'Service',
			),
			'weight' => 60,
		),

		'languages' => array(
			'title' => __('Languages'),
			'url' => array(
				'admin' => true,
				'plugin' => 'settings',
				'controller' => 'languages',
				'action' => 'index',
			),
			'weight' => 70,
		),

	),
));
