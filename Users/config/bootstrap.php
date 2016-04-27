<?php

use Cake\Core\Configure;
use Croogo\Core\Cache\CroogoCache;
use Croogo\Core\Croogo;

Croogo::hookApiComponent('Croogo/Users.Users', 'Users.UserApi');

/**
 * Failed login attempts
 *
 * Default is 5 failed login attempts in every 5 minutes
 */
$cacheConfig = array_merge(
    Configure::read('Croogo.Cache.defaultConfig'),
    ['groups' => ['users']]
);
$failedLoginDuration = 300;
Configure::write('User.failed_login_limit', 5);
Configure::write('User.failed_login_duration', $failedLoginDuration);
CroogoCache::config('users_login', array_merge($cacheConfig, [
    'duration' => '+' . $failedLoginDuration . ' seconds',
    'groups' => ['users'],
]));

Croogo::hookAdminRowAction('Croogo/Users.Admin/Users/index', 'Reset Password', [
    'admin:true/plugin:users/controller:users/action:reset_password/:id' => [
        'title' => false,
        'options' => [
            'icon' => 'unlock',
            'tooltip' => [
                'data-title' => __d('croogo', 'Reset password'),
            ],
        ],
    ],
]);

Croogo::hookComponent('*', 'Croogo/Users.LoggedInUser');
