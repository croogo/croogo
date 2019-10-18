<?php

namespace Croogo\Menus\Controller\Api\V10;

use Cake\Event\Event;
use Croogo\Core\Controller\Api\AppController;

/**
 * Links Controller
 */
class LinksController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'index',
        ]);
    }

    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            $sort = $this->getRequest()->getQuery('sort');
            if (!$sort) {
                $event->getSubject()->query->sortBy('lft', 'ASC');
            }
        });
        return $this->Crud->execute();
    }

}
