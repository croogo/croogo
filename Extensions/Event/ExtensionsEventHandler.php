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
		);
	}

/**
 * onBootstrapComplete
 */
	public function onBootstrapComplete($event) {
		CroogoPlugin::cacheDependencies();
	}

}
