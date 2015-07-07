<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Croogo\Core\CroogoRouter;

Router::plugin('Croogo/Users', function (RouteBuilder $routeBuilder) {
	$routeBuilder->fallbacks();
});

CroogoRouter::mapResources('Users.Users', array(
	'prefix' => '/:api/:prefix/',
));

Router::connect('/:api/:prefix/users/lookup', array(
	'plugin' => 'users',
	'controller' => 'users',
	'action' => 'lookup',
), array(
	'routeClass' => 'Croogo\Core\Routing\Route\ApiRoute',
));

// Users
CroogoRouter::connect('/register', array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'add'));

CroogoRouter::connect('/user/:username', array(
	'plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'view'), array('pass' => array('username')
));

CroogoRouter::connect('/users/:controller/:action/*', array(
	'plugin' => 'Croogo/Users'
));

CroogoRouter::connect('/admin/users/:controller/:action/*', [
	'prefix' => 'admin',
	'plugin' => 'Croogo/Users'
]);
