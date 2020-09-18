<?php
declare(strict_types=1);

namespace Croogo\Core\Controller\Api;

use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Event\EventInterface;

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
            'loginAction' => [
                'prefix' => 'Admin',
                'plugin' => 'Croogo/Users',
                'controller' => 'Users',
                'action' => 'login',
            ],
        ];

        if (Plugin::isLoaded('ADmad/JwtAuth')) {
            $authConfig['authenticate']['ADmad/JwtAuth.Jwt'] = [
                'fields' => [
                    'username' => 'id',
                ],
                'parameter' => 'token',
                'queryDatasource' => true,
            ];
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

        $this->loadComponent('Auth', $this->setupAuthConfig());
        $this->loadComponent('RequestHandler');

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
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        if (Configure::read('Site.status') == 0 &&
            $this->Auth->user('role_id') != 1
        ) {
            if (!$this->getRequest()->is('whitelisted')) {
                $this->response = $this->response->withStatus(503);
            }
        }
    }
}
