<?php

App::uses('CakeEventListener', 'Event');

/**
 * Comments Event Handler
 *
 * @category Event
 * @package  Croogo.Taxonomy.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentsEventHandler implements CakeEventListener {

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
	public function onSetupAdminData() {
		CroogoNav::add('sidebar', 'content.children.comments', array(
			'title' => __d('croogo', 'Comments'),
			'url' => array(
				'admin' => true,
				'plugin' => 'comments',
				'controller' => 'comments',
				'action' => 'index',
			),
			'children' => array(
				'published' => array(
					'title' => __d('croogo', 'Published'),
					'url' => array(
						'admin' => true,
						'plugin' => 'comments',
						'controller' => 'comments',
						'action' => 'index',
						'?' => array(
							'status' => '1',
						),
					),
				),
				'approval' => array(
					'title' => __d('croogo', 'Approval'),
					'url' => array(
						'admin' => true,
						'plugin' => 'comments',
						'controller' => 'comments',
						'action' => 'index',
						'?' => array(
							'status' => '0',
						),
					),
				),
			),
		));
	}

}
