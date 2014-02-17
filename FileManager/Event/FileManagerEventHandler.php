<?php

App::uses('CakeEventListener', 'Event');

/**
 * FileManagerEventHandler
 *
 * @package  Croogo.FileManager.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FileManagerEventHandler implements CakeEventListener {

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
		CroogoNav::add('sidebar', 'media', array(
			'icon' => array('picture', 'large'),
			'title' => __d('croogo', 'Media'),
			'url' => array(
				'admin' => true,
				'plugin' => 'file_manager',
				'controller' => 'attachments',
				'action' => 'index',
			),
			'weight' => 40,
			'children' => array(
				'attachments' => array(
					'title' => __d('croogo', 'Attachments'),
					'url' => array(
						'admin' => true,
						'plugin' => 'file_manager',
						'controller' => 'attachments',
						'action' => 'index',
					),
				),
				'file_manager' => array(
					'title' => __d('croogo', 'File Manager'),
					'url' => array(
						'admin' => true,
						'plugin' => 'file_manager',
						'controller' => 'file_manager',
						'action' => 'browse',
					),
				),
			),
		));
	}

}
