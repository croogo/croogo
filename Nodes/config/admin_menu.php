<?php

namespace Croogo\Nodes\Config;

use Croogo\Core\CroogoNav;

CroogoNav::add('sidebar', 'content', array(
	'icon' => 'edit',
	'title' => __d('croogo', 'Content'),
	'url' => array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Nodes',
		'controller' => 'Nodes',
		'action' => 'index',
	),
	'weight' => 10,
	'children' => array(
		'list' => array(
			'title' => __d('croogo', 'List'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Nodes',
				'controller' => 'Nodes',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'create' => array(
			'title' => __d('croogo', 'Create'),
			'url' => array(
				'prefix' => 'admin',
				'plugin' => 'Croogo/Nodes',
				'controller' => 'Nodes',
				'action' => 'create',
			),
			'weight' => 20,
		),
	)
));
