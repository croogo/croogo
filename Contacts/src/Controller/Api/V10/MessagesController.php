<?php
declare(strict_types=1);

namespace Croogo\Contacts\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Messages Controller
 *
 * @property \Croogo\Contacts\Model\Table\MessagesTable $Messages
 * @method \Croogo\Contacts\Model\Entity\Message[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MessagesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        return $this->Crud->execute();
    }

    /**
     * View method
     *
     * @param string|null $id Message id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        return $this->Crud->execute();
    }

}
