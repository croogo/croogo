<?php

namespace Croogo\Wysiwyg\Event;

use Cake\Event\EventListenerInterface;
use Croogo\Core\Croogo;

/**
 * Wysiwyg Event Handler
 *
 * @category Event
 * @package  Croogo.Ckeditor
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class WysiwygEventHandler implements EventListenerInterface
{

/**
 * implementedEvents
 *
 * @return array
 */
    public function implementedEvents()
    {
        return [
            'Croogo.bootstrapComplete' => [
                'callable' => 'onBootstrapComplete',
            ],
        ];
    }

    public function onBootstrapComplete($event)
    {
        Croogo::hookHelper('*', 'Croogo/Wysiwyg.Wysiwyg');
    }
}
