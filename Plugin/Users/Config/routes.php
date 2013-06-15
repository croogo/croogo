<?php

// Users
CroogoRouter::connect('/register', array('plugin' => 'users', 'controller' => 'users', 'action' => 'add'));

CroogoRouter::connect('/user/:username', array(
	'plugin' => 'users', 'controller' => 'users', 'action' => 'view'), array('pass' => array('username')
));

CroogoRouter::connect('/admin', array('admin'=>true,'plugin' => 'users', 'controller' => 'users', 'action' => 'login'));