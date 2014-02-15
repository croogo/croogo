<?php

Croogo::hookApiComponent('Users', 'Users.UserApi');

/**
 * Failed login attempts
 *
 * Default is 5 failed login attempts in every 5 minutes
 */
$cacheConfig = array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('users'))
);
$failedLoginDuration = 300;
Configure::write('User.failed_login_limit', 5);
Configure::write('User.failed_login_duration', $failedLoginDuration);
CroogoCache::config('users_login', array_merge($cacheConfig, array(
	'duration' => '+' . $failedLoginDuration . ' seconds',
	'groups' => array('users'),
)));

Croogo::hookAdminRowAction('Users/admin_index', 'Reset Password', array(
	'admin:true/plugin:users/controller:users/action:reset_password/:id' => array(
		'title' => false,
		'options' => array(
			'icon' => 'unlock',
			'tooltip' => array(
				'data-title' => __d('croogo', 'Reset password'),
			),
		),
	),
));
