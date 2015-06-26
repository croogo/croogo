<?php

App::uses('CakeEventListener', 'Event');

/**
 * DashboardsEventHandler
 *
 * @package  Croogo.Dashboards.Event
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardsEventHandler implements CakeEventListener {

/**
 * implementedEvents
 */
	public function implementedEvents() {
		return array(
			'Croogo.setupAdminDashboardData' => array(
				'callable' => 'onSetupAdminDashboardData',
			),
		);
	}

/**
 * Setup admin data
 */
	public function onSetupAdminDashboardData($event) {
		$plugins = CakePlugin::loaded();
		$config = 'Config' . DS . 'admin_dashboard.php';
		foreach ($plugins as $plugin) {
			$file = CakePlugin::path($plugin) . $config;
			if (file_exists($file)) {
				Configure::load($plugin .'.' . 'admin_dashboard', 'dashboards');
			}
		}
	}

}
