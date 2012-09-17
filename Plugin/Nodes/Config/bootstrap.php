<?php

Croogo::hookComponent('*', 'Nodes.Nodes');

Croogo::hookHelper('*', 'Nodes.Nodes');

CroogoNav::add('content', array(
	'icon' => array('edit', 'large'),
	'title' => __('Content'),
	'url' => array(
		'plugin' => 'nodes',
		'admin' => true,
		'controller' => 'nodes',
		'action' => 'index',
	),
	'weight' => 10,
	'children' => array(

		'list' => array(
			'title' => __('List'),
			'url' => array(
				'plugin' => 'nodes',
				'admin' => true,
				'controller' => 'nodes',
				'action' => 'index',
			),
			'weight' => 10,
		),

		'create' => array(
			'title' => __('Create'),
			'url' => array(
				'plugin' => 'nodes',
				'admin' => true,
				'controller' => 'nodes',
				'action' => 'create',
			),
			'weight' => 20,
		),

	)
));
