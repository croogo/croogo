<?php
declare(strict_types=1);

/**
 * Croogo : A CakePHP powered Content Management System (http://www.croogo.org)
 * Copyright (c) Fahad Ibnay Heylaal <contact@fahad19.com>
 * Copyright (c) Rachman Chavik <rchavik@gmail.com>
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @since    1.5
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
namespace Croogo\Core\Controller\Admin;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Croogo\Core\Controller\AppController as CroogoAppController;
use Croogo\Core\Croogo;
use Crud\Controller\ControllerTrait;

/**
 * Croogo Admin App Controller
 *
 * @property \Crud\Controller\Component\CrudComponent $Crud
 */
class AppController extends CroogoAppController
{
    use ControllerTrait;

    /**
     * Load the theme component with the admin theme specified
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Crud.Crud', [
            'actions' => [
                'index' => [
                    'className' => 'Croogo/Core.Admin/Index'
                ],
                'lookup' => [
                    'className' => 'Crud.Lookup',
                    'findMethod' => 'all'
                ],
                'view' => [
                    'className' => 'Crud.View'
                ],
                'add' => [
                    'className' => 'Croogo/Core.Admin/Add',
                    'messages' => [
                        'success' => [
                            'text' => __d('croogo', '{name} created successfully'),
                            'params' => [
                                'type' => 'success',
                                'class' => ''
                            ]
                        ],
                        'error' => [
                            'params' => [
                                'type' => 'error',
                                'class' => ''
                            ]
                        ]
                    ]
                ],
                'edit' => [
                    'className' => 'Croogo/Core.Admin/Edit',
                    'messages' => [
                        'success' => [
                            'text' => __d('croogo', '{name} updated successfully'),
                            'params' => [
                                'type' => 'success',
                                'class' => ''
                            ]
                        ],
                        'error' => [
                            'params' => [
                                'type' => 'error',
                                'class' => ''
                            ]
                        ]
                    ]
                ],
                'toggle' => [
                    'className' => 'Croogo/Core.Admin/Toggle'
                ],
                'delete' => [
                    'className' => 'Crud.Delete'
                ]
            ],
            'listeners' => [
                'Crud.Redirect',
                'Crud.Search',
                'Crud.RelatedModels',
                'Croogo/Core.Flash',
            ]
        ]);

        $this->Theme->setConfig('theme', Configure::read('Site.admin_theme'));
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->viewBuilder()->setLayout('admin');

        parent::beforeFilter($event);

        Croogo::dispatchEvent('Croogo.beforeSetupAdminData', $this);
    }

    public function index()
    {
        return $this->Crud->execute();
    }

    public function view($id)
    {
        return $this->Crud->execute();
    }

    public function add()
    {
        return $this->Crud->execute();
    }

    public function edit($id)
    {
        return $this->Crud->execute();
    }

    public function delete($id)
    {
        return $this->Crud->execute();
    }

    protected function redirectToSelf(EventInterface $event)
    {
        $subject = $event->getSubject();
        if ($subject->success) {
            $data = $this->getRequest()->getData();
            if (isset($data['_apply'])) {
                $entity = $subject->entity;

                return $this->redirect(['action' => 'edit', $entity->id]);
            }
        }
    }
}
