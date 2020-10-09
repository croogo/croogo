<?php
declare(strict_types=1);

namespace Croogo\Taxonomy\Controller\Api\V10;

use Cake\Event\EventInterface;
use Croogo\Core\Controller\Api\AppController;

/**
 * Taxonomies Controller
 */
class TaxonomiesController extends AppController
{

    public function index()
    {
        $this->Crud->on('beforePaginate', function (EventInterface $event) {
            return $event->getSubject()->query
                ->leftJoinWith('Terms')
                ->leftJoinWith('Vocabularies');
        });
        return $this->Crud->execute();
    }

    public function view()
    {
        return $this->Crud->execute();
    }

}
