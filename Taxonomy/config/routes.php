<?php

use Croogo\Croogo\CroogoRouter;

CroogoRouter::connect('/admin/terms/:action/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Taxonomy', 'controller' => 'Terms',
));
CroogoRouter::connect('/admin/types/:action/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Taxonomy', 'controller' => 'Types',
));
CroogoRouter::connect('/admin/vocabularies/:action/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Taxonomy', 'controller' => 'Vocabularies',
));
