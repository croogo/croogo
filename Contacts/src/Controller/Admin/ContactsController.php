<?php
declare(strict_types=1);

namespace Croogo\Contacts\Controller\Admin;

use Cake\Event\EventInterface;

/**
 * Contacts Controller
 *
 * @category Controller
 * @package  Croogo.Contacts.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ContactsController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        $this->Crud->setConfig('actions.index', [
            'displayFields' => $this->Contacts->displayFields()
        ]);
    }

    public function implementedEvents(): array
    {
        return parent::implementedEvents() + [
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }

    public function beforeCrudRedirect(EventInterface $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }
}
