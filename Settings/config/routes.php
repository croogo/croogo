<?php

use Croogo\Croogo\CroogoRouter;

CroogoRouter::connect('/admin/settings/:controller/:action/*', [
	'prefix' => 'admin',
	'plugin' => 'Croogo/Settings',
]);
