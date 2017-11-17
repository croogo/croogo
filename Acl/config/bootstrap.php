<?php

use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Configure;
use Croogo\Core\Croogo;

if (Configure::read('Site.acl_plugin') == 'Croogo/Acl') {
    // activate AclFilter component only until after a succesfull install
    if (Configure::read('Croogo.installed')) {
        Croogo::hookComponent('*', 'Croogo/Acl.Filter');
        Croogo::hookComponent('*', 'Croogo/Acl.Access');
    }

    Croogo::hookBehavior('Croogo/Users.Users', 'Croogo/Acl.UserAro', ['priority' => 20]);
    Croogo::hookBehavior('Croogo/Users.Roles', 'Croogo/Acl.RoleAro', ['priority' => 20]);

    $defaultCacheConfig = Configure::read('Croogo.Cache.defaultConfig');
    Cache::config('permissions', [
        'duration' => '+1 hour',
        'path' => CACHE . 'acl' . DS,
        'groups' => ['acl']
    ] + $defaultCacheConfig);

    if (Configure::read('Access Control.multiRole')) {
        Configure::write('Acl.classname', App::className('Croogo/Acl.HabtmDbAcl', 'Adapter'));
    }
}
