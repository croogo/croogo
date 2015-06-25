<?php

namespace Croogo\Extensions\Config;

use Croogo\Croogo\CroogoNav;

CroogoNav::add('sidebar', 'extensions', array(
	'icon' => 'magic',
	'title' => __d('croogo', 'Extensions'),
	'url' => array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Extensions',
		'controller' => 'ExtensionsPlugins',
		'action' => 'index',
	),
	'weight' => 35,
	'children' => array(
		'themes' => array(
			'title' => __d('croogo', 'Themes'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Extensions',
				'controller' => 'ExtensionsThemes',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'locales' => array(
			'title' => __d('croogo', 'Locales'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Extensions',
				'controller' => 'ExtensionsLocales',
				'action' => 'index',
			),
			'weight' => 20,
		),
		'plugins' => array(
			'title' => __d('croogo', 'Plugins'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Extensions',
				'controller' => 'ExtensionsPlugins',
				'action' => 'index',
			),
			'htmlAttributes' => array(
				'class' => 'separator',
			),
			'weight' => 30,
		),
	),
));
