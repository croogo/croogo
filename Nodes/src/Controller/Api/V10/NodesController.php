<?php

namespace Croogo\Nodes\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Nodes Controller
 */
class NodesController extends AppController
{

    public function lookup()
    {
        return $this->Crud->execute();
    }

}
