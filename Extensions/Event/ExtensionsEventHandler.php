<?php

namespace Croogo\Extensions\Event;
App::uses('CakeEventListener', 'Event');

/**
 * ExtensionsEventHandler
 *
 * @package  Croogo.Extensions.Event
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtensionsEventHandler implements CakeEventListener {

/**
 * implementedEvents
 */
	public function implementedEvents() {
		return array(
			'Croogo.bootstrapComplete' => array(
				'callable' => 'onBootstrapComplete',
			),
			'Croogo.beforeSetupAdminData' => array(
				'callable' => 'onBeforeSetupAdminData',
			),
			'Croogo.setupAdminData' => array(
				'callable' => 'onSetupAdminData',
			),
		);
	}

/**
 * Before Setup admin data
 */
	public function onBeforeSetupAdminData($event) {
		$plugins = CakePlugin::loaded();
		$config = 'Config' . DS . 'admin.php';
		foreach ($plugins as $plugin) {
			$file = CakePlugin::path($plugin) . $config;
			if (file_exists($file)) {
				require $file;
			}
		}
	}

/**
 * Setup admin data
 */
	public function onSetupAdminData($event) {
		$plugins = CakePlugin::loaded();
		$config = 'Config' . DS . 'admin_menu.php';
		foreach ($plugins as $plugin) {
			$file = CakePlugin::path($plugin) . $config;
			if (file_exists($file)) {
				require $file;
			}
		}
	}

/**
 * onBootstrapComplete
 */
	public function onBootstrapComplete($event) {
		CroogoPlugin::cacheDependencies();
	}

}
