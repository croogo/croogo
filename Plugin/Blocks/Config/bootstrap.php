<?php

Croogo::hookComponent('*', array(
	'Blocks.Blocks' => array(
		'priority' => 5,
		)
	)
);

Croogo::hookHelper('*', 'Blocks.Regions');

CroogoNav::add('blocks', array(
	'icon' => array('columns', 'large'),
	'title' => __('Blocks'),
	'url' => array(
		'plugin' => 'blocks',
		'admin' => true,
		'controller' => 'blocks',
		'action' => 'index',
	),
	'weight' => 30,
	'children' => array(
		'blocks' => array(
			'title' => __('Blocks'),
			'url' => array(
				'plugin' => 'blocks',
				'admin' => true,
				'controller' => 'blocks',
				'action' => 'index',
			),
		),
		'regions' => array(
			'title' => __('Regions'),
			'url' => array(
				'plugin' => 'blocks',
				'admin' => true,
				'controller' => 'regions',
				'action' => 'index',
			),
		),
	),
));
