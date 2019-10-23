<?php

namespace Croogo\Taxonomy\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Types Controller
 */
class TypesController extends AppController
{

    public function index()
    {
        return $this->Crud->execute();
    }

}

