<?php

Croogo::hookControllerProperty('*', 'uses', array('Contents.Node'));

Croogo::hookComponent('*', 'Contents.Contents');

CroogoNav::add('content', array(
	'title' => __('Content'),
	'url' => array(
		'plugin' => 'contents',
		'admin' => true,
		'controller' => 'nodes',
		'action' => 'index',
	),
	'weight' => 10,
	'children' => array(

		'list' => array(
			'title' => __('List'),
			'url' => array(
				'plugin' => 'contents',
				'admin' => true,
				'controller' => 'nodes',
				'action' => 'index',
			),
			'weight' => 10,
		),

		'create' => array(
			'title' => __('Create'),
			'url' => array(
				'plugin' => 'contents',
				'admin' => true,
				'controller' => 'nodes',
				'action' => 'create',
			),
			'weight' => 20,
		),

	)
));
