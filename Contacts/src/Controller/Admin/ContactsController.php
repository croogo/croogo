<?php

namespace Croogo\Contacts\Controller\Admin;

/**
 * Contacts Controller
 *
 * @category Controller
 * @package  Croogo.Contacts.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ContactsController extends AppController
{
    public $modelClass = 'Croogo/Contacts.Contacts';

    public function initialize()
    {
        parent::initialize();

        $this->Crud->config('actions.index', [
            'displayFields' => $this->Contacts->displayFields()
        ]);
    }
}
