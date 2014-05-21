<?php

namespace Croogo\Acl\Config;
CroogoNav::add('sidebar', 'users.children.permissions', array(
	'title' => __d('croogo', 'Permissions'),
	'url' => array(
		'admin' => true,
		'plugin' => 'acl',
		'controller' => 'acl_permissions',
		'action' => 'index',
	),
	'weight' => 30,
));

CroogoNav::add('sidebar', 'settings.children.acl', array(
	'title' => __d('croogo', 'Access Control'),
	'url' => array(
		'admin' => true,
		'plugin' => 'settings',
		'controller' => 'settings',
		'action' => 'prefix',
		'Access Control',
	),
));
