<?php

namespace Croogo\Users\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class LoggedInUserComponent extends Component
{

    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        /** @var Controller $controller */
        $controller = $event->getSubject();

        $controller->set('loggedIn', (bool)$controller->Auth->user());

        if (!$controller->Auth->user()) {
            return;
        }

        $users = \Cake\ORM\TableRegistry::getTableLocator()->get('Croogo/Users.Users');
        $controller->set('loggedInUser', $users->get($controller->Auth->user('id')));
    }
}
