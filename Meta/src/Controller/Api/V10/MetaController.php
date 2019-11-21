<?php

namespace Croogo\Meta\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Meta Controller
 */
class MetaController extends AppController
{

    public function index()
    {
        return $this->Crud->execute();
    }

}
