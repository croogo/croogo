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

CroogoRouter::connect('/admin/users/index', [
	'plugin' => 'Croogo/Users',
	'prefix' => 'admin',
	'controller' => 'Users',
	'action' => 'index'
]);

CroogoRouter::connect('/admin/users/add', [
	'plugin' => 'Croogo/Users',
	'prefix' => 'admin',
	'controller' => 'Users',
	'action' => 'add'
]);

CroogoRouter::connect('/admin/users/edit/*', [
	'plugin' => 'Croogo/Users',
	'prefix' => 'admin',
	'controller' => 'Users',
	'action' => 'edit'
]);

CroogoRouter::connect('/admin/users/delete/*', [
	'plugin' => 'Croogo/Users',
	'prefix' => 'admin',
	'controller' => 'Users',
	'action' => 'delete'
]);
