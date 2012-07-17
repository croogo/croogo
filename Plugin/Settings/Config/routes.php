<?php

CroogoRouter::connect('/admin', array(
	'admin' => true, 'plugin' => 'settings', 'controller' => 'settings', 'action' => 'dashboard'
));
