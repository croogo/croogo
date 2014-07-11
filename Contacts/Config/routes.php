<?php

use Croogo\Croogo\CroogoRouter;

CroogoRouter::connect('/contact/*', array(
	'plugin' => 'contacts', 'controller' => 'contacts', 'action' => 'view',
));
