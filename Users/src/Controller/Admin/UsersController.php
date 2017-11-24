<?php

namespace Croogo\Users\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Email\Email;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Cake\Utility\Text;
use Croogo\Core\Croogo;
use Croogo\Users\Model\Entity\User;

/**
 * Users Controller
 *
 * @category Controller
 * @package  Croogo.Users.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UsersController extends AppController
{

    public $modelClass = 'Croogo/Users.Users';

    public $paginate = [
        'limit' => 10,
        'order' => [
            'id' => 'DESC',
        ],
    ];

/**
 * Initialize
 *
 * @return void
 */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');

        $this->Crud->config('actions.index', [
            'displayFields' => $this->Users->displayFields(),
            'searchFields' => ['role_id', 'name']
        ]);

        $this->Crud->config('actions.edit', [
            'editfields' => $this->Users->editFields(),
            'saveOptions' => [
                'associated' => [
                    'Roles',
                ],
            ],
        ]);

        $this->Crud->config('actions.add', [
            'saveOptions' => [
                'associated' => [
                    'Roles',
                ],
            ],
        ]);

        $this->Crud->addListener('Crud.Api');
        $this->Crud->addListener('Croogo/Core.Chooser');

        $this->_setupPrg();

        $this->Auth->allow(['register', 'forgot', 'reset']);
    }

/**
 * implementedEvents
 *
 * @return array
 */
    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Controller.Users.beforeAdminLogin' => 'onBeforeAdminLogin',
            'Controller.Users.adminLoginFailure' => 'onAdminLoginFailure',
            'Croogo.beforeSetupAdminData' => 'beforeSetupAdminData',
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeLookup' => 'beforeLookup',
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
            'Crud.beforeSave' => 'beforeCrudSave',
            'Crud.afterSave' => 'afterCrudSave',
        ];
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);

        $this->Crud->on('relatedModel', function(Event $event) {
            if ($event->subject()->name == 'Roles') {
                $event->subject()->query = $this->Users->Roles
                    ->find('roleHierarchy')
                    ->order([
                        'ParentAro.lft' => 'DESC',
                    ])
                    ->find('list');
            }
        });

    }

    public function beforeSetupAdminData()
    {
        $this->Auth->allow('resetPassword');
    }

/**
 * Notify user when failed_login_limit hash been hit
 *
 * @return bool
 */
    public function onBeforeAdminLogin()
    {
        $field = $this->Auth->config('authenticate.all.fields.username');
        if (empty($this->request->data)) {
            return true;
        }
        $cacheName = 'auth_failed_' . $this->request->data[$field];
        $cacheValue = Cache::read($cacheName, 'users_login');
        if ($cacheValue >= Configure::read('User.failed_login_limit')) {
            $this->Flash->error(__d('croogo', 'You have reached maximum limit for failed login attempts. Please try again after a few minutes.'));
            return $this->redirect(['action' => $this->request->param('action')]);
        }
        return true;
    }

/**
 * Record the number of times a user has failed authentication in cache
 *
 * @return bool
 * @access public
 */
    public function onAdminLoginFailure()
    {
        $field = $this->Auth->config('authenticate.all.fields.username');
        if (empty($this->request->data)) {
            return true;
        }
        $cacheName = 'auth_failed_' . $this->request->data[$field];
        $cacheValue = Cache::read($cacheName, 'users_login');
        Cache::write($cacheName, (int)$cacheValue + 1, 'users_login');
        return true;
    }

    /**
     * @param \Cake\Event\Event $event Event object
     * @return void
     */
    public function beforeCrudSave(Event $event)
    {
        /**
         * @var \Croogo\Users\Model\Entity\User
         */
        $entity = $event->subject()->entity;
        if (!$entity->isNew() && $entity->has('activation_key')) {
            return;
        }

        $entity->activation_key = $this->Users->generateActivationKey();
    }

    public function afterCrudSave(Event $event) {
        if ($event->subject()->success && $event->subject()->created) {
            if ($this->request->data('notification') != null) {
                $this->Users->sendActivationEmail($event->subject()->entity);
            }
        }
    }

    public function beforeCrudRedirect(Event $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }

/**
 * Admin reset password
 *
 * @param int $id
 * @return void
 * @access public
 */
    public function resetPassword($id = null)
    {
        $user = $this->Users->get($id);

        if ($this->request->is('put')) {
            $user = $this->Users->patchEntity($user, $this->request->data());

            if ($this->Users->save($user)) {
                $this->Flash->success(__d('croogo', 'Password has been reset.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('croogo', 'Password could not be reset. Please, try again.'));
            }
        }

        $this->set('user', $user);
    }

/**
 * Admin login
 *
 * @return void
 * @access public
 */
    public function login()
    {
        $this->viewBuilder()->setLayout('admin_login');

        if ($this->Auth->user('id')) {
            if (!$this->request->session()->check('Flash.auth') &&
                !$this->request->session()->check('Flash.flash')
            ) {
                $this->Flash->error(__d('croogo', 'You are already logged in'), ['key' => 'auth']);
            }
            return $this->redirect($this->Auth->redirectUrl());
        }

        $session = $this->request->session();
        $redirectUrl = $this->Auth->redirectUrl();
        if ($redirectUrl && !$session->check('Croogo.redirect')) {
            $session->write('Croogo.redirect', $redirectUrl);
        }

        if ($this->request->is('post')) {
            Croogo::dispatchEvent('Controller.Users.beforeAdminLogin', $this);
            $user = $this->Auth->identify();
            if ($user) {

                if ($session->check('Croogo.redirect')) {
                    $redirectUrl = $session->read('Croogo.redirect');
                    $session->delete('Croogo.redirect');
                } else {
                    $redirectUrl = $this->Auth->redirectUrl();
                }

                if (!$this->Access->isUrlAuthorized($user, $redirectUrl)) {
                    Croogo::dispatchEvent('Controller.Users.adminLoginFailure', $this);
                    $this->Auth->authError = __d('croogo', 'Authorization error');
                    $this->Flash->error($this->Auth->authError, ['key' => 'auth']);
                    return $this->redirect($this->Auth->loginAction);
                }

                $this->Auth->setUser($user);

                if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
                    $user = $this->Users->get($user['id']);
                    $user->password = $this->request->data('password');
                    $this->Users->save($user);
                }

                Croogo::dispatchEvent('Controller.Users.adminLoginSuccessful', $this);
                return $this->redirect($redirectUrl);
            } else {
                Croogo::dispatchEvent('Controller.Users.adminLoginFailure', $this);
                $this->Auth->authError = __d('croogo', 'Incorrect username or password');
                $this->Flash->error($this->Auth->authError, ['key' => 'auth']);
                return $this->redirect($this->Auth->config('loginAction'));
            }
        }
    }

/**
 * Admin logout
 *
 * @return void
 * @access public
 */
    public function logout()
    {
        Croogo::dispatchEvent('Controller.Users.adminLogoutSuccessful', $this);
        $this->Flash->success(__d('croogo', 'Log out successful.'), ['key' => 'auth']);
        return $this->redirect($this->Auth->logout());
    }

    public function beforeLookup(Event $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $query
            ->select([
                'id',
                'username',
                'name',
                'website',
                'image',
                'bio',
                'timezone',
                'status',
                'created',
                'updated',
            ])
            ->contain([
            'Roles' => [
                'fields' => [
                    'id',
                    'title',
                    'alias'
                ],
            ],
        ]);
    }

    public function beforePaginate(Event $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $multiRole = Configure::read('Access Control.multiRole');
        if ($multiRole) {
            $query
                ->leftJoinWith('Roles')
                ->distinct();
        } else {
            $query
                ->contain('Roles');
        }

        $roles = $this->Users->Roles
            ->find('roleHierarchy')
            ->order([
                'ParentAro.lft' => 'DESC',
            ])
            ->find('list');
        $this->set(compact('roles'));
    }

    protected function _getSenderEmail()
    {
        return 'croogo@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
    }

/**
 * Register
 *
 * @return void
 * @access public
 */
    public function register()
    {
        if ($this->Auth->user('id')) {
            if (!$this->request->session()->check('Flash.auth') &&
                !$this->request->session()->check('Flash.flash')
            ) {
                $this->Flash->error(__d('croogo', 'You are already logged in'));
            }
            return $this->redirect($this->referer());
        }
        $user = $this->Users->newEntity();

        $this->set('user', $user);

        if (!$this->request->is('post')) {
            return;
        }

        $user = $this->Users->register($user, $this->request->data());
        if (!$user) {
            $this->Flash->error(__d('croogo', 'The User could not be saved. Please, try again.'));

            return;
        }

        $this->Flash->success(__d('croogo', 'You have successfully registered an account. An email has been sent with further instructions.'));

        return $this->redirect(['action' => 'login']);
    }

/**
 * Forgot
 *
 * @return void
 * @access public
 */
    public function forgot()
    {
        if (!$this->request->is('post')) {
            return;
        }

        $username = $this->request->data('username');
        if (!$username) {
            $this->Flash->error(__d('croogo', 'Invalid username.'));
            return $this->redirect(['action' => 'forgot']);
        }

        $user = $this->Users
            ->find()
            ->where(['username' => $username])
            ->orWhere(['email' => $username])
            ->first();
        if (!$user) {
            $this->Flash->error(__d('croogo', 'Invalid username.'));
            return $this->redirect(['action' => 'forgot']);
        }

        $success = $this->Users->resetPassword($user);
        if (!$success) {
            $this->Flash->error(__d('croogo', 'An error occurred. Please try again.'));
            return;
        }

        $this->Flash->success(__d('croogo', 'An email has been sent with instructions for resetting your password.'));

        return $this->redirect(['action' => 'login']);
    }

/**
 * Reset
 *
 * @param string $username
 * @param string $activationKey
 * @return void
 * @access public
 */
    public function reset($username, $activationKey)
    {
        // Get the user with the activation key from the database
        $user = $this->Users->find()->where([
            'username' => $username,
            'activation_key' => $activationKey
        ])->first();
        if (!$user) {
            $this->Flash->error(__d('croogo', 'An error occurred.'));

            return $this->redirect(['action' => 'login']);
        }

        $this->set('user', $user);

        if (!$this->request->is('put')) {
            return;
        }

        // Change the password of the user entity
        $user = $this->Users->changePasswordFromReset($user, $this->request->data());

        // Save the user with changed password
        $user = $this->Users->save($user);
        if (!$user) {
            $this->Flash->error(__d('croogo', 'An error occurred. Please try again.'));

            return;
        }

        $this->Flash->success(__d('croogo', 'Your password has been reset successfully.'));

        return $this->redirect(['action' => 'login']);
    }

}
