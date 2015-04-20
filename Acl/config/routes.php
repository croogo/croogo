<?php

use Croogo\Croogo\CroogoRouter;

CroogoRouter::connect('/admin/acl/:controller/:action/*', [
	'prefix' => 'admin',
	'plugin' => 'Croogo/Acl'
]);
