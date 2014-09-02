<?php

App::uses('CroogoDashboard', 'Dashboard.Lib');

/**
 * Dashboard URL
 */
Configure::write('Croogo.dashboardUrl', array(
	'admin' => true,
	'plugin' => 'dashboard',
	'controller' => 'dashboard_dashboard',
	'action' => 'index',
));