<?php

namespace Croogo\Menus\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Menus Controller
 */
class MenusController extends AppController
{

    public function index()
    {
        return $this->Crud->execute();
    }

}
