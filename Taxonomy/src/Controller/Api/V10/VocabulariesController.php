<?php

namespace Croogo\Taxonomy\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Vocabularies Controller
 */
class VocabulariesController extends AppController
{

    public function index()
    {
        return $this->Crud->execute();
    }

}

