<?php

namespace Croogo\Nodes\Config;
CroogoNav::add('sidebar', 'content', array(
	'icon' => array('edit', 'large'),
	'title' => __d('croogo', 'Content'),
	'url' => array(
		'admin' => true,
		'plugin' => 'nodes',
		'controller' => 'nodes',
		'action' => 'index',
	),
	'weight' => 10,
	'children' => array(
		'list' => array(
			'title' => __d('croogo', 'List'),
			'url' => array(
				'admin' => true,
				'plugin' => 'nodes',
				'controller' => 'nodes',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'create' => array(
			'title' => __d('croogo', 'Create'),
			'url' => array(
				'admin' => true,
				'plugin' => 'nodes',
				'controller' => 'nodes',
				'action' => 'create',
			),
			'weight' => 20,
		),
	)
));
