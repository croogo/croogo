<?php

namespace Croogo\Wysiwyg\Event;
App::uses('CakeEventListener', 'Event');

/**
 * Wysiwyg Event Handler
 *
 * @category Event
 * @package  Croogo.Ckeditor
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class WysiwygEventHandler implements CakeEventListener {

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Croogo.bootstrapComplete' => array(
				'callable' => 'onBootstrapComplete',
			),
		);
	}

	public function onBootstrapComplete($event) {
		Croogo::hookHelper('*', 'Wysiwyg.Wysiwyg');
	}

}
