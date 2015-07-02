<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/contact/*', array(
	'plugin' => 'Croogo/Contacts', 'controller' => 'Contacts', 'action' => 'view',
));

CroogoRouter::connect('/admin/contacts/:controller/:action/*', [
	'prefix' => 'admin',
	'plugin' => 'Croogo/Contacts',
]);
