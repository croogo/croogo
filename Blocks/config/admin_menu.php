<?php

namespace Croogo\Blocks\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'blocks', array(
	'icon' => 'columns',
	'title' => __d('croogo', 'Blocks'),
	'url' => array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Blocks',
		'controller' => 'Blocks',
		'action' => 'index',
	),
	'weight' => 30,
	'children' => array(
		'blocks' => array(
			'title' => __d('croogo', 'Blocks'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Blocks',
				'controller' => 'Blocks',
				'action' => 'index',
			),
		),
		'regions' => array(
			'title' => __d('croogo', 'Regions'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Blocks',
				'controller' => 'Regions',
				'action' => 'index',
			),
		),
	),
));
