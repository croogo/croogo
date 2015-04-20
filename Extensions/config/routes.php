<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
use Croogo\Croogo\CroogoRouter;

CroogoRouter::connect('/admin', Configure::read('Croogo.dashboardUrl'));

CroogoRouter::connect('/admin/extensions/:controller/:action/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Extensions'
));
