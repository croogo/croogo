<?php

namespace Croogo\Settings\Config;

use Croogo\Croogo\CroogoNav;

CroogoNav::add('sidebar', 'settings', array(
	'icon' => array('cog', 'large'),
	'title' => __d('croogo', 'Settings'),
	'url' => array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Settings',
		'controller' => 'Settings',
		'action' => 'prefix',
		'Site',
	),
	'weight' => 60,
	'children' => array(
		'site' => array(
			'title' => __d('croogo', 'Site'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Settings',
				'controller' => 'Settings',
				'action' => 'prefix',
				'Site',
			),
			'weight' => 10,
		),

		'meta' => array(
			'title' => __d('croogo', 'Meta'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Settings',
				'controller' => 'Settings',
				'action' => 'prefix',
				'Meta',
			),
			'weight' => 20,
		),

		'reading' => array(
			'title' => __d('croogo', 'Reading'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Settings',
				'controller' => 'Settings',
				'action' => 'prefix',
				'Reading',
			),
			'weight' => 30,
		),

		'writing' => array(
			'title' => __d('croogo', 'Writing'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Settings',
				'controller' => 'Settings',
				'action' => 'prefix',
				'Writing',
			),
			'weight' => 40,
		),

		'comment' => array(
			'title' => __d('croogo', 'Comment'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Settings',
				'controller' => 'Settings',
				'action' => 'prefix',
				'Comment',
			),
			'weight' => 50,
		),

		'service' => array(
			'title' => __d('croogo', 'Service'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Settings',
				'controller' => 'Settings',
				'action' => 'prefix',
				'Service',
			),
			'weight' => 60,
		),

		'languages' => array(
			'title' => __d('croogo', 'Languages'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Settings',
				'controller' => 'Settings',
				'action' => 'index',
			),
			'weight' => 70,
		),

	),
));
