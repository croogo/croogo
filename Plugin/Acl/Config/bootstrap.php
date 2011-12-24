<?php

CroogoNav::add('users.children.permissions', array(
	'title' => __('Permissions'),
	'url' => array(
		'admin' => true,
		'plugin' => 'acl',
		'controller' => 'acl_permissions',
		'action' => 'index',
		),
	'access' => array('admin'),
	'weight' => 30,
	));
