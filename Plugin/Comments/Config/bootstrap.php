<?php

Croogo::hookHelper('*', 'Comments.Comments');

CroogoNav::add('content.children.comments', array(
	'title' => __('Comments'),
	'url' => array(
		'admin' => true,
		'plugin' => 'comments',
		'controller' => 'comments',
		'action' => 'index',
	),
	'children' => array(
		'published' => array(
			'title' => __('Published'),
			'url' => array(
				'admin' => true,
				'plugin' => 'comments',
				'controller' => 'comments',
				'action' => 'index',
				'status' => '1',
			),
		),
		'approval' => array(
			'title' => __('Approval'),
			'url' => array(
				'admin' => true,
				'plugin' => 'comments',
				'controller' => 'comments',
				'action' => 'index',
				'status' => '0',
			),
		),
	),
));
