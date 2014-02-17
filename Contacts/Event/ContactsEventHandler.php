<?php

App::uses('CakeEventListener', 'Event');

/**
 * Contacts Event Handler
 *
 * @category Component
 * @package  Croogo.Contacts.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ContactsEventHandler implements CakeEventListener {

/**
 * implementEvents
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
 *
 */
	public function onSetupAdminData() {
		CroogoNav::add('sidebar', 'contacts', array(
			'icon' => array('comments', 'large'),
			'title' => __d('croogo', 'Contacts'),
			'url' => array(
				'admin' => true,
				'plugin' => 'contacts',
				'controller' => 'contacts',
				'action' => 'index',
			),
			'weight' => 50,
			'children' => array(
				'contacts' => array(
					'title' => __d('croogo', 'Contacts'),
					'url' => array(
						'admin' => true,
						'plugin' => 'contacts',
						'controller' => 'contacts',
						'action' => 'index',
					),
				),
				'messages' => array(
					'title' => __d('croogo', 'Messages'),
					'url' => array(
						'admin' => true,
						'plugin' => 'contacts',
						'controller' => 'messages',
						'action' => 'index',
					),
				),
			),
		));
	}

}
