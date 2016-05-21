<?php

namespace Croogo\Users\Controller\Admin;

/**
 * Roles Controller
 *
 * @category Controller
 * @package  Croogo.Users.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RolesController extends AppController
{
    public $modelClass = 'Croogo/Users.Roles';

    public function initialize()
    {
        parent::initialize();

        $this->Crud->config('actions.index', [
            'displayFields' => $this->Roles->displayFields()
        ]);
    }
}
