<?php

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
			'Croogo.setupAdminData' => array(
				'callable' => 'onSetupAdminData',
			),
		);
	}

/**
 * Setup admin data
 */
	public function onSetupAdminData($event) {
		CroogoNav::add('sidebar', 'extensions', array(
			'icon' => array('magic', 'large'),
			'title' => __d('croogo', 'Extensions'),
			'url' => array(
				'plugin' => 'extensions',
				'controller' => 'extensions_plugins',
				'action' => 'index',
			),
			'weight' => 35,
			'children' => array(
				'themes' => array(
					'title' => __d('croogo', 'Themes'),
					'url' => array(
						'plugin' => 'extensions',
						'controller' => 'extensions_themes',
						'action' => 'index',
					),
					'weight' => 10,
				),
				'locales' => array(
					'title' => __d('croogo', 'Locales'),
					'url' => array(
						'plugin' => 'extensions',
						'controller' => 'extensions_locales',
						'action' => 'index',
					),
					'weight' => 20,
				),
				'plugins' => array(
					'title' => __d('croogo', 'Plugins'),
					'url' => array(
						'plugin' => 'extensions',
						'controller' => 'extensions_plugins',
						'action' => 'index',
					),
					'htmlAttributes' => array(
						'class' => 'separator',
					),
					'weight' => 30,
				),
			),
		));
	}

/**
 * onBootstrapComplete
 */
	public function onBootstrapComplete($event) {
		CroogoPlugin::cacheDependencies();
	}

}
