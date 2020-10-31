<?php
declare(strict_types=1);

namespace Croogo\Contacts\Controller\Api\V10;

use Croogo\Core\Controller\Api\AppController;

/**
 * Contacts Controller
 *
 * @property \Croogo\Contacts\Model\Table\ContactsTable $Contacts
 * @method \Croogo\Contacts\Model\Entity\Contact[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ContactsController extends AppController
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
     * @param string|null $id Contact id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        return $this->Crud->execute();
    }

}
