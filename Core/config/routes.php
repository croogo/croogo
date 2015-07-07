<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/admin/link-chooser/*', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/Core',
	'controller' => 'LinkChooser',
	'action' => 'linkChooser'
));
