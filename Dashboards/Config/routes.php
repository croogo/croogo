<?php

CroogoRouter::connect('/admin/dashboards', array(
	'admin' => true,
	'plugin' => 'dashboards',
	'controller' => 'dashboards_dashboards',
	'action' => 'dashboard',
));

CroogoRouter::connect('/admin/dashboards/:action/*', array(
	'admin' => true,
	'plugin' => 'dashboards',
	'controller' => 'dashboards_dashboards',
), array(
	'action' => '[a-zA-Z0-9_-]+',
));
