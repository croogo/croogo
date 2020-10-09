<?php
declare(strict_types=1);

namespace Croogo\Blocks\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Regions Controller
 */
class RegionsController extends AppController
{

    public function index()
    {
        return $this->Crud->execute();
    }

    public function view()
    {
        return $this->Crud->execute();
    }

}
