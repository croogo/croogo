<?php

namespace Croogo\Taxonomy\Controller\Api\V10;

use Cake\Event\Event;
use Croogo\Core\Controller\Api\AppController;

/**
 * Taxonomies Controller
 */
class TaxonomiesController extends AppController
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
            return $event->getSubject()->query
                ->leftJoinWith('Terms')
                ->leftJoinWith('Vocabularies');
        });
        return $this->Crud->execute();
    }

}
