<?php

Croogo::hookControllerProperty('*', 'uses', array('Blocks.Block'));

Croogo::hookComponent('*', array(
	'Blocks.Blocks' => array(
		'priority' => 5,
		)
	)
);

CroogoNav::add('blocks', array(
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
