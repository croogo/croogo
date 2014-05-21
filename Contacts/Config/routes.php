<?php

CroogoRouter::connect('/contact/*', array(
	'plugin' => 'contacts', 'controller' => 'contacts', 'action' => 'view',
));
