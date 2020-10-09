<?php
declare(strict_types=1);

namespace Croogo\Menus\Controller\Api\V10;

use Cake\Event\Event;
use Croogo\Core\Controller\Api\AppController;

/**
 * Links Controller
 */
class LinksController extends AppController
{

    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            /** @var \Cake\Datasource\QueryInterface $query */
            $query = $event->getSubject()->query;
            $sort = $this->getRequest()->getQuery('sort');
            if (!$sort) {
                $query->order(['lft' => 'ASC']);
            }
        });
        return $this->Crud->execute();
    }

}
