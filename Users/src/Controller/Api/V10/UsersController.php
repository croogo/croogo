<?php

namespace Croogo\Users\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Users Controller
 */
class UsersController extends AppController
{

    public function lookup()
    {
        return $this->Crud->execute();
    }

}
