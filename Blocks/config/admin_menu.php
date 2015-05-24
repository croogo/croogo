<?php

namespace Croogo\Blocks\Config;

use Croogo\Croogo\CroogoNav;

CroogoNav::add('sidebar', 'blocks', array(
	'icon' => array('columns', 'large'),
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
