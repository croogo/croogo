<?php

if (Configure::read('Site.acl_plugin') == 'Acl') {

	// activate AclFilter component only until after a succesful install
	if (file_exists(APP . 'Config' . DS . 'settings.json')) {
		Croogo::hookComponent('*', 'Acl.AclFilter');
		Croogo::hookComponent('*', array(
			'CroogoAccess' => array(
				'className' => 'Acl.AclAccess',
			),
		));
	}

	Croogo::hookBehavior('User', 'Acl.UserAro');
	Croogo::hookBehavior('Role', 'Acl.RoleAro');

	CroogoNav::add('users.children.permissions', array(
		'title' => __d('croogo', 'Permissions'),
		'url' => array(
			'admin' => true,
			'plugin' => 'acl',
			'controller' => 'acl_permissions',
			'action' => 'index',
		),
		'weight' => 30,
	));

	CroogoNav::add('settings.children.acl', array(
		'title' => __d('croogo', 'Access Control'),
		'url' => array(
			'admin' => true,
			'plugin' => 'settings',
			'controller' => 'settings',
			'action' => 'prefix',
			'Access Control',
		),
	));

	Cache::config('permissions', array(
		'duration' => '+1 hour',
		'path' => CACHE . 'queries' . DS,
		'engine' => Configure::read('Cache.defaultEngine'),
		'prefix' => Configure::read('Cache.defaultPrefix'),
		'groups' => array('acl')
	));
}
