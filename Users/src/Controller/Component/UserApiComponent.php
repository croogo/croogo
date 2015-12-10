<?php

namespace Croogo\Users\Controller\Component;

use Cake\Controller\Controller;
use Croogo\Core\Controller\Component\BaseApiComponent;

class UserApiComponent extends BaseApiComponent
{

/**
 * API Version
 */
    protected $_apiVersion = 'v1.0';

/**
 * API methods
 */
    protected $_apiMethods = [
        'lookup',
    ];

/**
 * List users with filter capability as defined in User::$filterArgs
 *
 * This will be useful for ajax autocompletion
 */
    public function lookup(Controller $controller)
    {
        $request = $controller->request;
        $controller->Prg->commonProcess();

        $controller->User->Behaviors->attach('Users.UserApiResultFormatter');
        $controller->paginate = [
            'fields' => [
                'id', 'username', 'name', 'website', 'image', 'bio', 'timezone',
                'status', 'created', 'updated',
            ],
            'contain' => [
                'Role' => [
                    'fields' => ['id', 'title', 'alias'],
                ],
            ],
            'conditions' => $controller->User->parseCriteria($request->query),
        ];

        $users = $controller->paginate();

        $controller->set('_rootNode', 'users');
        $controller->set('user', $users);
        $controller->set('_serialize', 'user');
    }
}
