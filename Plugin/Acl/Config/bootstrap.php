<?php

if (Configure::read('Site.acl_plugin') == 'Acl') {

	// activate AclFilter component only until after a succesful install
	if (file_exists(APP . 'Config' . DS . 'settings.yml')) {
		Croogo::hookComponent('*', 'Acl.AclFilter');
		Croogo::hookComponent('*', array(
			'CroogoAccess' => array(
				'className' => 'Acl.AclAccess',
				),
			));
	}

	Croogo::hookBehavior('User', 'Acl.UserAco');
	Croogo::hookBehavior('Role', 'Acl.RoleAco');

	CroogoNav::add('users.children.permissions', array(
		'title' => __('Permissions'),
		'url' => array(
			'admin' => true,
			'plugin' => 'acl',
			'controller' => 'acl_permissions',
			'action' => 'index',
			),
		'weight' => 30,
		));

}
