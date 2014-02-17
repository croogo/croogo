<?php

App::uses('CakeEventListener', 'Event');

/**
 * MenusEventHandler
 *
 * @package  Croogo.Menus.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MenusEventHandler implements CakeEventListener {

/**
 * implementedEvents
 */
	public function implementedEvents() {
		return array(
			'Croogo.setupAdminData' => array(
				'callable' => 'onSetupAdminData',
			),
		);
	}

/**
 * Setup admin data
 */
	public function onSetupAdminData($event) {
		CroogoNav::add('sidebar', 'menus', array(
			'icon' => array('sitemap', 'large'),
			'title' => __d('croogo', 'Menus'),
			'url' => array(
				'plugin' => 'menus',
				'admin' => true,
				'controller' => 'menus',
				'action' => 'index',
			),
			'weight' => 20,
			'children' => array(
				'menus' => array(
					'title' => __d('croogo', 'Menus'),
					'url' => array(
						'plugin' => 'menus',
						'admin' => true,
						'controller' => 'menus',
						'action' => 'index',
					),
					'weight' => 10,
				),
				'add_new' => array(
					'title' => __d('croogo', 'Add new'),
					'url' => array(
						'plugin' => 'menus',
						'admin' => true,
						'controller' => 'menus',
						'action' => 'add',
					),
					'weight' => 20,
					'htmlAttributes' => array('class' => 'separator'),
				),
			),
		));
	}

}
