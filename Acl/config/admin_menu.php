<?php

namespace Croogo\Acl\Config;

use Croogo\Core\Nav;

Nav::add('sidebar', 'users.children.permissions', array(
	'title' => __d('croogo', 'Permissions'),
	'url' => array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Acl',
		'controller' => 'Permissions',
		'action' => 'index',
	),
	'weight' => 30,
));

Nav::add('sidebar', 'settings.children.acl', array(
	'title' => __d('croogo', 'Access Control'),
	'url' => array(
		'prefix' => 'admin',
		'plugin' => 'Croogo/Settings',
		'controller' => 'Settings',
		'action' => 'prefix',
		'Access Control',
	),
));
