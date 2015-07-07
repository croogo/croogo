<?php

namespace Croogo\Translate\Event;

use Cake\Event\EventListener;
use Translate\Lib\Translations;
/**
 * TranslateEventHandler
 *
 * @package  Croogo.Translate.Event
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TranslateEventHandler implements EventListener {

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
