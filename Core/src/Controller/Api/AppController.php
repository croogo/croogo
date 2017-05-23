<?php

namespace Croogo\Core\Controller\Api;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Event\Event;
use Croogo\Core\Croogo;
use Croogo\Core\Controller\AppController as CroogoAppController;

/**
 * Base Api Controller
 *
 */
class AppController extends CroogoAppController
{

/**
 * Initialize
 *
 * @return void
 */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Crud.Crud', [
            'actions' => [
                'index' => [
                    'className' => 'Crud.Index',
                ],
                'lookup' => [
                    'className' => 'Crud.Lookup',
                    'findMethod' => 'all'
                ],
                'view' => [
                    'className' => 'Crud.View',
                ],
                'add' => [
                    'className' => 'Crud.Add',
                ],
                'edit' => [
                    'className' => 'Crud.Edit',
                ],
                'delete' => [
                    'className' => 'Crud.Delete'
                ]
            ],
            'listeners' => [
                'Crud.Search',
                'Crud.RelatedModels',
                'Crud.Api',
            ]
        ]);

        Configure::write('debug', false);

    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->config('unauthorizedRedirect', false);
        $this->Auth->config('loginRedirect', false);

        if (Configure::read('Site.status') == 0 &&
            $this->Auth->user('role_id') != 1
        ) {
            if (!$this->request->is('whitelisted')) {
                $this->response->statusCode(503);
            }
        }
    }

}
