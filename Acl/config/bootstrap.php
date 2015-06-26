<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Core\Croogo;

if (Configure::read('Site.acl_plugin') == 'Croogo/Acl') {

	// activate AclFilter component only until after a succesful install
	if (file_exists(APP . 'config' . DS . 'settings.json')) {
		Croogo::hookComponent('*', 'Croogo/Acl.AclFilter');
		Croogo::hookComponent('*', array(
			'CroogoAccess' => array(
				'className' => 'Croogo/Acl.AclAccess',
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
