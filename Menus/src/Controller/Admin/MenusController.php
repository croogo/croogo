<?php

namespace Croogo\Menus\Controller\Admin;

use Cake\Event\EventInterface;

/**
 * Menus Controller
 *
 * @category Controller
 * @package  Croogo.Menus.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MenusController extends AppController
{

    public function implementedEvents(): array
    {
        return parent::implementedEvents() + [
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }

    public function initialize(): void
    {
        parent::initialize();
        if ($this->getRequest()->getParam('action') === 'toggle') {
            $this->Croogo->protectToggleAction();
        }
    }

    /**
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function beforeCrudRedirect(EventInterface $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }
}
