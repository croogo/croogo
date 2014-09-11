<?php

/**
 * Dashboard URL
 */
Configure::write('Croogo.dashboardUrl', array(
	'admin' => true,
	'plugin' => 'dashboards',
	'controller' => 'dashboards_dashboards',
	'action' => 'index',
));

App::uses('CroogoDashboard', 'Dashboards.Lib');
App::uses('DashboardsConfigReader', 'Dashboards.Configure');
Configure::config('dashboards', new DashboardsConfigReader());