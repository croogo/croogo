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

	Cache::config('permissions', array(
		'duration' => '+1 hour',
		'path' => CACHE . 'queries' . DS,
		'engine' => Configure::read('Cache.defaultEngine'),
		'prefix' => Configure::read('Cache.defaultPrefix'),
		'groups' => array('acl')
	));
}
