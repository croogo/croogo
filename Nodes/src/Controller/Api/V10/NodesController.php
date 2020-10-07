<?php
declare(strict_types=1);

namespace Croogo\Nodes\Controller\Api\V10;

use Cake\Event\Event;
use Croogo\Core\Controller\Api\AppController;

/**
 * Nodes Controller
 */
class NodesController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();
        $this->Auth->allow([
            'index',
        ]);
    }

    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            $event->getSubject()->query
                ->find('view')
                ->contain(['Users']);
        });
        return $this->Crud->execute();
    }

    public function lookup()
    {
        // FIXME: Things get broken when Translate is activated
        $this->Nodes->behaviors()->reset();
        $this->Nodes->addBehavior('Search.Search');
        $this->Nodes->associations()->remove('I18n');

        return $this->Crud->execute();
    }
}
