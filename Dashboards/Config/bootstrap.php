<?php

App::uses('CroogoDashboard', 'Dashboards.Lib');

/**
 * Dashboard URL
 */
Configure::write('Croogo.dashboardUrl', array(
	'admin' => true,
	'plugin' => 'dashboards',
	'controller' => 'dashboard_boxes',
	'action' => 'index',
));