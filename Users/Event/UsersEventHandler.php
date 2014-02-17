<?php

App::uses('CakeEventListener', 'Event');

/**
 * UsersEventHandler
 *
 * @package  Croogo.Users.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UsersEventHandler implements CakeEventListener {

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
		CroogoNav::add('sidebar', 'users', array(
			'icon' => array('user', 'large'),
			'title' => __d('croogo', 'Users'),
			'url' => array(
				'admin' => true,
				'plugin' => 'users',
				'controller' => 'users',
				'action' => 'index',
			),
			'weight' => 50,
			'children' => array(
				'users' => array(
					'title' => __d('croogo', 'Users'),
					'url' => array(
						'admin' => true,
						'plugin' => 'users',
						'controller' => 'users',
						'action' => 'index',
					),
					'weight' => 10,
				),
				'roles' => array(
					'title' => __d('croogo', 'Roles'),
					'url' => array(
						'admin' => true,
						'plugin' => 'users',
						'controller' => 'roles',
						'action' => 'index',
					),
					'weight' => 20,
				),
			),
		));
	}

}
