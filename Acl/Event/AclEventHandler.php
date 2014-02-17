<?php

App::uses('CakeEventListener', 'Event');

/**
 * AclEventHandler
 *
 * @package  Croogo.Acl.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclEventHandler implements CakeEventListener {

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
		CroogoNav::add('sidebar', 'users.children.permissions', array(
			'title' => __d('croogo', 'Permissions'),
			'url' => array(
				'admin' => true,
				'plugin' => 'acl',
				'controller' => 'acl_permissions',
				'action' => 'index',
			),
			'weight' => 30,
		));

		CroogoNav::add('sidebar', 'settings.children.acl', array(
			'title' => __d('croogo', 'Access Control'),
			'url' => array(
				'admin' => true,
				'plugin' => 'settings',
				'controller' => 'settings',
				'action' => 'prefix',
				'Access Control',
			),
		));
	}

}
