<?php
declare(strict_types=1);

namespace Croogo\Taxonomy\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Terms Controller
 */
class TermsController extends AppController
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
