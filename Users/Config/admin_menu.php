<?php

CroogoNav::add('sidebar', 'users', array(
	'icon' => array('user', 'large'),
	'title' => __d('croogo', 'Users'),
	'url' => array(
		'admin' => true,
		'plugin' => 'users',
		'controller' => 'users',
		'action' => 'index',
	),
	'weight' => 50,
	'children' => array(
		'users' => array(
			'title' => __d('croogo', 'Users'),
			'url' => array(
				'admin' => true,
				'plugin' => 'users',
				'controller' => 'users',
				'action' => 'index',
			),
			'weight' => 10,
		),
		'roles' => array(
			'title' => __d('croogo', 'Roles'),
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
