<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Croogo\Croogo;

if (Configure::read('Site.acl_plugin') == 'Acl') {

	// activate AclFilter component only until after a succesful install
	if (file_exists(APP . 'config' . DS . 'settings.json')) {
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
		'className' => Configure::read('Croogo.Cache.defaultEngine'),
		'prefix' => Configure::read('Croogo.Cache.defaultPrefix'),
		'groups' => array('acl')
	));
}
