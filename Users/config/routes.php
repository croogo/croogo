<?php

use Cake\Routing\Router;
use Croogo\Croogo\CroogoRouter;

CroogoRouter::mapResources('Users.Users', array(
	'prefix' => '/:api/:prefix/',
));

Router::connect('/:api/:prefix/users/lookup', array(
	'plugin' => 'users',
	'controller' => 'users',
	'action' => 'lookup',
), array(
	'routeClass' => 'Croogo\Croogo\Routing\Route\ApiRoute',
));

// Users
CroogoRouter::connect('/register', array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'add'));

CroogoRouter::connect('/user/:username', array(
	'plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'view'), array('pass' => array('username')
));

CroogoRouter::connect('/admin/users/:controller/:action/*', [
	'prefix' => 'admin',
	'plugin' => 'Croogo/Users',
	'action' => 'index'
]);
