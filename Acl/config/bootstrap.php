<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Core\Croogo;

if (Configure::read('Site.acl_plugin') == 'Croogo/Acl') {
    // activate AclFilter component only until after a succesfull install
    if (Configure::read('Site.status')) {
        Croogo::hookComponent('*', 'Croogo/Acl.Filter');
        Croogo::hookComponent('*', [
            'CroogoAccess' => [
                'className' => 'Croogo/Acl.Access',
            ],
        ]);
    }

//	Croogo::hookBehavior('Croogo/Users.Users', 'Croogo/Acl.UserAro');
//	Croogo::hookBehavior('Croogo/Users.Roles', 'Croogo/Acl.RoleAro');

    Cache::config('permissions', [
        'duration' => '+1 hour',
        'path' => CACHE . 'queries' . DS,
        'className' => Configure::read('Croogo.Cache.defaultEngine'),
        'prefix' => Configure::read('Croogo.Cache.defaultPrefix'),
        'groups' => ['acl']
    ]);
}
