<?php

App::uses('CakeEventListener', 'Event');

/**
 * BlocksEventHandler
 *
 * @package  Croogo.Blocks.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class BlocksEventHandler implements CakeEventListener {

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
		CroogoNav::add('sidebar', 'blocks', array(
			'icon' => array('columns', 'large'),
			'title' => __d('croogo', 'Blocks'),
			'url' => array(
				'plugin' => 'blocks',
				'admin' => true,
				'controller' => 'blocks',
				'action' => 'index',
			),
			'weight' => 30,
			'children' => array(
				'blocks' => array(
					'title' => __d('croogo', 'Blocks'),
					'url' => array(
						'plugin' => 'blocks',
						'admin' => true,
						'controller' => 'blocks',
						'action' => 'index',
					),
				),
				'regions' => array(
					'title' => __d('croogo', 'Regions'),
					'url' => array(
						'plugin' => 'blocks',
						'admin' => true,
						'controller' => 'regions',
						'action' => 'index',
					),
				),
			),
		));
	}

}
