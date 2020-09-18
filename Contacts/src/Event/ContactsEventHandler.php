<?php
declare(strict_types=1);

namespace Croogo\Contacts\Event;

use Cake\Event\EventListenerInterface;

/**
 * Contacts Event Handler
 *
 * @category Component
 * @package  Croogo.Contacts.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ContactsEventHandler implements EventListenerInterface
{

    /**
     * implementEvents
     */
    public function implementedEvents(): array
    {
        return [];
    }
}
