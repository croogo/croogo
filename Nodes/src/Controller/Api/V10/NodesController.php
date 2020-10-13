<?php
declare(strict_types=1);

namespace Croogo\Nodes\Controller\Api\V10;

use Cake\Event\EventInterface;
use Croogo\Core\Utility\StringConverter;
use Croogo\Core\Controller\Api\AppController;

/**
 * Nodes Controller
 */
class NodesController extends AppController
{

    public function index()
    {
        $this->Crud->on('afterPaginate', function (EventInterface $event) {
            $entities = $event->getSubject()->entities;
            $stringConverter = new StringConverter();
            foreach ($entities as $entity) {
                if (empty($entity->excerpt)) {
                    $entity->excerpt = $stringConverter->firstPara($entity->body);
                }
            }
        });
        return $this->Crud->execute();
    }

    public function view()
    {
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
