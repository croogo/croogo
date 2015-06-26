<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/menus/menus/:action/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Menus', 'controller' => 'Menus',
));

CroogoRouter::connect('/admin/menus/links/:action/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Menus', 'controller' => 'Links',
));
