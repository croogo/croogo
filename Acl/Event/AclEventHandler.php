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
			'Dispatcher.beforeDispatch' => array(
				'callable' => 'onBeforeDispatch',
				'priority' => 11,
			),
		);
	}

/**
 * Dispatcher.beforeDispatch handler
 */
	public function onBeforeDispatch($event) {
		if (!Configure::read('Access Control.splitSession')) {
			return;
		}
		$request = $event->data['request'];
		$cookiePath = $request->base . '/' . $request->param('prefix');
		Croogo::mergeConfig('Session.ini', array(
			'session.cookie_path' => $cookiePath,
		));
	}

}
