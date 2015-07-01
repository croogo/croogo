<?php

use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/contact/*', array(
	'plugin' => 'contacts', 'controller' => 'contacts', 'action' => 'view',
));
