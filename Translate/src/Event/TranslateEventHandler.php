<?php

namespace Croogo\Translate\Event;

use Cake\Event\EventListenerInterface;
use Croogo\Translate\Translations;

/**
 * TranslateEventHandler
 *
 * @package  Croogo.Translate.Event
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TranslateEventHandler implements EventListenerInterface
{

    public function implementedEvents()
    {
        return [
            'Croogo.bootstrapComplete' => [
                'callable' => 'onCroogoBootstrapComplete',
            ],
        ];
    }

    public function onCroogoBootstrapComplete($event)
    {
        Translations::translateModels();
    }
}
