<?php

CroogoNav::add('users', array(
	'icon' => array('user', 'large'),
	'title' => __('Users'),
	'url' => array(
		'admin' => true,
		'plugin' => 'users',
		'controller' => 'users',
		'action' => 'index',
	),
	'weight' => 50,
	'children' => array(
		'users' => array(
			'title' => __('Users'),
			'url' => array(
				'admin' => true,
				'plugin' => 'users',
				'controller' => 'users',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'roles' => array(
			'title' => __('Roles'),
			'url' => array(
				'admin' => true,
				'plugin' => 'users',
				'controller' => 'roles',
				'action' => 'index',
			),
			'weight' => 20,
		),
	),
));
