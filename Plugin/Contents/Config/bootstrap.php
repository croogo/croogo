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

		'comments' => array(
			'title' => __('Comments'),
			'url' => array(
				'plugin' => false,
				'admin' => true,
				'controller' => 'comments',
				'action' => 'index',
			),
			'children' => array(
				'published' => array(
					'title' => __('Published'),
					'url' => array(
						'plugin' => false,
						'admin' => true,
						'controller' => 'comments',
						'action' => 'index',
						'filter' => 'status:1;',
					),
				),
				'approval' => array(
					'title' => __('Approval'),
					'url' => array(
						'plugin' => false,
						'admin' => true,
						'controller' => 'comments',
						'action' => 'index',
						'filter' => 'status:0;',
					),
				),
			),
		),
	),

));
