<?php

namespace Croogo\Users\Controller\Api\V10;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Security;
use Croogo\Core\Controller\Api\AppController;
use Firebase\JWT\JWT;

/**
 * Roles Controller
 */
class RolesController extends AppController
{

    public function index()
    {
        return $this->Crud->execute();
    }

}
