<?php

CroogoNav::add('sidebar', 'blocks', array(
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
