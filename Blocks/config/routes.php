<?php

use Croogo\Croogo\CroogoRouter;

CroogoRouter::connect('/admin/blocks/blocks/:action/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Blocks', 'controller' => 'Blocks',
));

CroogoRouter::connect('/admin/blocks/regions/:action/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Blocks', 'controller' => 'Regions',
));
