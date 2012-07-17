<?php

CroogoNav::add('settings', array(
	'title' => __('Settings'),
	'url' => array(
		'plugin' => false,
		'admin' => true,
		'controller' => 'settings',
		'action' => 'prefix',
		'Site',
	),
	'weight' => 60,
	'children' => array(
		'site' => array(
			'title' => __('Site'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Site',
			),
			'weight' => 10,
		),

		'meta' => array(
			'title' => __('Meta'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Meta',
			),
			'weight' => 20,
		),

		'reading' => array(
			'title' => __('Reading'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Reading',
			),
			'weight' => 30,
		),

		'writing' => array(
			'title' => __('Writing'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Writing',
			),
			'weight' => 40,
		),

		'comment' => array(
			'title' => __('Comment'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Comment',
			),
			'weight' => 50,
		),

		'service' => array(
			'title' => __('Service'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'settings',
				'action' => 'prefix',
				'Service',
			),
			'weight' => 60,
		),

		'languages' => array(
			'title' => __('Languages'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'languages',
				'action' => 'index',
			),
			'weight' => 70,
		),

	),
));
