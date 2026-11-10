<?php

namespace Croogo\Core\Controller\Api;

use Croogo\Core\Controller\Component\AuthComponent;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;

/**
 * Base Api Controller
 *
 */
class AppController extends Controller
{

    protected function setupAuthConfig()
    {
        $authConfig = [
            'authenticate' => [
                AuthComponent::ALL => [
                    'userModel' => 'Croogo/Users.Users',
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password',
                    ],
                    'passwordHasher' => [
                        'className' => 'Fallback',
                        'hashers' => ['Default', 'Weak'],
                    ],
                    'scope' => [
                        'Users.status' => true,
                    ],
                ],
                'Croogo/Acl.ApiForm',
            ],
            'authorize' => [
                AuthComponent::ALL => [
                    'actionPath' => 'controllers',
                    'userModel' => 'Croogo/Users.Users',
                ],
                'Croogo/Acl.AclCached' => [
                    'actionPath' => 'controllers',
                ]
            ],

            'unauthorizedRedirect' => false,
            'checkAuthInd' => 'Controller.initialize',
            'loginAction' => false,
        ];

        if (Plugin::isLoaded('ADmad/JwtAuth')) {
            $authConfig['authenticate']['ADmad/JwtAuth.Jwt'] = [
                'fields' => [
                    'username' => 'id',
                ],
                'parameter' => 'token',
                'queryDatasource' => true,
            ];

//            $authConfig['authorize']['ADmad/JwtAuth.Jwt'] = [
//                'actionPath' => 'controllers',
//            ];
        }

        return $authConfig;
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Croogo/Core.Auth', $this->setupAuthConfig());
        // Cake 5: RequestHandlerComponent usunięty — negocjacja treści JSON:API
        // przechodzi przez Crud/CrudJsonApi i ViewBuilder.

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
                'CrudJsonApi.JsonApi',
            ]
        ]);

        Configure::write('debug', false);
    }

    /**
     * Cake 5: RequestHandlerComponent usunięty. (a) Wybieramy JsonView dla każdego
     * żądania API — nie tylko .json: RequestHandler negocjował JSON także bez
     * rozszerzenia, a SPA/PWA wołają endpointy bez niego — o ile Crud/CrudJsonApi
     * nie ustawiło własnej klasy widoku, (b) mapujemy legacy zmienną widoku
     * `_serialize` na opcję widoku `serialize`. Dzięki temu kontrolery API w stylu
     * Cake 3 ($this->set(['_serialize' => [...]])) działają bez zmian.
     *
     * @param \Cake\Event\EventInterface $event
     * @return void
     */
    public function beforeRender(\Cake\Event\EventInterface $event): void
    {
        parent::beforeRender($event);

        $builder = $this->viewBuilder();
        if ($builder->getClassName() === null) {
            $builder->setClassName('Json');
        }

        $serialize = $builder->getVar('_serialize');
        if ($serialize !== null && $builder->getOption('serialize') === null) {
            $builder->setOption('serialize', $serialize);
        }
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);

        if (Configure::read('Site.status') == 0 &&
            $this->Auth->user('role_id') != 1
        ) {
            if (!$this->getRequest()->is('whitelisted')) {
                $this->setResponse($this->getResponse()->withStatus(503));
            }
        }
    }
}
