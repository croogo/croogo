<?php

CroogoCache::config('croogo_blocks', array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('blocks'))
));

Croogo::hookComponent('*', array(
	'Blocks.Blocks' => array(
		'priority' => 5,
	)
));

Croogo::hookHelper('*', 'Blocks.Regions');

CroogoNav::add('blocks', array(
	'icon' => array('columns', 'large'),
	'title' => __d('croogo', 'Blocks'),
	'url' => array(
		'plugin' => 'blocks',
		'admin' => true,
		'controller' => 'blocks',
		'action' => 'index',
	),
	'weight' => 30,
	'children' => array(
		'blocks' => array(
			'title' => __d('croogo', 'Blocks'),
			'url' => array(
				'plugin' => 'blocks',
				'admin' => true,
				'controller' => 'blocks',
				'action' => 'index',
			),
		),
		'regions' => array(
			'title' => __d('croogo', 'Regions'),
			'url' => array(
				'plugin' => 'blocks',
				'admin' => true,
				'controller' => 'regions',
				'action' => 'index',
			),
		),
	),
));
