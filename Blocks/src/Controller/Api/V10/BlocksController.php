<?php

namespace Croogo\Blocks\Controller\Api\V10;

use Cake\Event\Event;
use Croogo\Core\Controller\Api\AppController;
use Croogo\Core\Croogo;

/**
 * Blocks Controller
 */
class BlocksController extends AppController
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
        return $this->Crud->execute();
    }

}
