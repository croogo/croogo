<?php

namespace Croogo\Translate\Event;
App::uses('CakeEventListener', 'Event');
App::uses('Translations', 'Translate.Lib');

/**
 * TranslateEventHandler
 *
 * @package  Croogo.Translate.Event
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TranslateEventHandler implements CakeEventListener {

	public function implementedEvents() {
		return array(
			'Croogo.bootstrapComplete' => array(
				'callable' => 'onCroogoBootstrapComplete',
			),
		);
	}

	public function onCroogoBootstrapComplete($event) {
		Translations::translateModels();
	}

}
