<?php

App::uses('CakeEventListener', 'Event');
App::uses('Cache', 'Cache');

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
			'Controller.Links.afterPublish' => array(
				'callable' => 'onAfterBulkProcess',
			),
			'Controller.Links.afterUnpublish' => array(
				'callable' => 'onAfterBulkProcess',
			),
			'Controller.Links.afterDelete' => array(
				'callable' => 'onAfterBulkProcess',
			),
		);
	}

/**
 * Clear Links related cache after bulk operation
 *
 * @param CakeEvent $event
 * @return void
 */
	public function onAfterBulkProcess($event) {
		Cache::clearGroup('menus', 'croogo_menus');
	}

}
