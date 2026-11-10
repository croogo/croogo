<?php

namespace Croogo\Users\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Routing\Router;
use Croogo\Core\Croogo;

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

    public ?string $modelClass = 'Croogo/Users.Users';

    public array $paginate = [
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
    public function initialize(): void
    {
        parent::initialize();

        //$this->loadComponent('RequestHandler');

        $this->Crud->setConfig('actions.index', [
            'displayFields' => $this->Users->displayFields(),
            'searchFields' => ['role_id', 'name']
        ]);

        $this->Crud->setConfig('actions.edit', [
            'editfields' => $this->Users->editFields(),
            'saveOptions' => [
                'associated' => [
                    'Roles',
                ],
            ],
        ]);

        $this->Crud->setConfig('actions.add', [
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
    public function implementedEvents(): array
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

    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Crud->on('relatedModel', function (Event $event): void {
            if ($event->getSubject()->name == 'Roles') {
                $event->getSubject()->query = $this->Users->Roles
                    ->find('roleHierarchy')
                    ->orderBy([
                        'ParentAro.lft' => 'DESC',
                    ])
                    ->find('list');
            }
        });
    }

    public function beforeSetupAdminData(): void
    {
        $this->Auth->allow('resetPassword');
    }

    /**
     * Notify user when failed_login_limit hash been hit
     *
     * @return bool
     */
    public function onBeforeAdminLogin(Event $event): void
    {
        // Cake 5.2: zwracanie wartosci z listenera deprecated -> setResult()
        $field = $this->Auth->getConfig('authenticate.all.fields.username');
        $data = $this->getRequest()->getData();
        if (empty($data)) {
            return;
        }
        $cacheName = 'auth_failed_' . $data[$field];
        $cacheValue = Cache::read($cacheName, 'users_login');
        // Both sides typed on purpose: with the limit unset `null >= null` is TRUE in PHP and
        // every login would be refused as "limit reached" before identify() even ran.
        // A limit of 0 (or unset) therefore means: lockout disabled.
        $limit = (int)Configure::read('User.failed_login_limit');
        if ($limit > 0 && (int)$cacheValue >= $limit) {
            $this->Flash->error(__d('croogo', 'You have reached maximum limit for failed login attempts. Please try again after a few minutes.'));

            $event->setResult($this->redirect(['action' => $this->getRequest()->getParam('action')]));
        }
    }

    /**
     * Record the number of times a user has failed authentication in cache
     *
     * @return bool
     * @access public
     */
    public function onAdminLoginFailure(): void
    {
        $field = $this->Auth->getConfig('authenticate.all.fields.username');
        if (empty($this->getRequest()->getData())) {
            return;
        }
        $cacheName = 'auth_failed_' . $this->getRequest()->getData($field);
        $cacheValue = Cache::read($cacheName, 'users_login');
        Cache::write($cacheName, (int)$cacheValue + 1, 'users_login');
    }

    /**
     * Forget the failed-login count of the account that just proved its password. Without this
     * the counter only ever grew until its TTL ran out: four typos followed by the right password
     * still left the next five minutes one mistake away from the lockout, and once the lockout
     * really blocks login() anyone who knows a username could keep the account locked by
     * submitting garbage - the legitimate owner's correct password would never reset it.
     *
     * @param mixed $login Username as submitted with the form.
     * @return void
     */
    protected function clearFailedLoginCount($login): void
    {
        if (!is_string($login) || $login === '') {
            return;
        }
        Cache::delete('auth_failed_' . $login, 'users_login');
    }

    /**
     * @param \Cake\Event\Event $event Event object
     * @return void
     */
    public function beforeCrudSave(Event $event): void
    {
        /**
         * @var \Croogo\Users\Model\Entity\User
         */
        $entity = $event->getSubject()->entity;
        if (!$entity->isNew() && $entity->has('activation_key')) {
            return;
        }

        $entity->activation_key = $this->Users->generateActivationKey();
    }

    public function afterCrudSave(Event $event): void
    {
//        if ($event->getSubject()->success && $event->getSubject()->created) {
//            if ($this->getRequest()->getData('notification') != null) {
//                $this->Users->sendActivationEmail($event->getSubject()->entity);
//            }
//        }
    }

    public function beforeCrudRedirect(Event $event): void
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }

    /**
     * Admin reset password
     *
     * @param int $id
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function resetPassword($id = null)
    {
        $user = $this->Users->get($id);

        if ($this->getRequest()->is('put')) {
            $user = $this->Users->patchEntity($user, $this->getRequest()->getData());

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
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function login()
    {
        $this->viewBuilder()->setLayout('admin_login');

        if ($this->Auth->user('id')) {
            if (!$this->getRequest()->getSession()->check('Flash.auth') &&
                !$this->getRequest()->getSession()->check('Flash.flash')
            ) {
                $this->Flash->error(__d('croogo', 'You are already logged in'), ['key' => 'auth']);
            }

            return $this->redirect($this->Auth->redirectUrl());
        }

        $session = $this->getRequest()->getSession();
        $redirectUrl = $this->Auth->redirectUrl();
        if ($redirectUrl && !$session->check('Croogo.redirect')) {
            $session->write('Croogo.redirect', $redirectUrl);
        }

        if (!$this->getRequest()->is('post')) {
            return;
        }

        $event = Croogo::dispatchEvent('Controller.Users.beforeAdminLogin', $this);
        if ($event->getResult() instanceof Response) {
            // A listener already answered (the failed-login limit in onBeforeAdminLogin). Without
            // this return identify() and setUser() kept running and, because Cake 5's
            // Controller::redirect() never overwrites a Location header once set, correct
            // credentials still logged the account in behind the "limit reached" banner.
            return $event->getResult();
        }

        $user = $this->Auth->identify();
        if (!$user) {
            Croogo::dispatchEvent('Controller.Users.adminLoginFailure', $this);
            // PHP 8.2+: authError to konfiguracja komponentu, nie property
            $this->Auth->setConfig('authError', __d('croogo', 'Incorrect username or password'));
            $this->Flash->error($this->Auth->getConfig('authError'), ['key' => 'auth']);

            return $this->redirect($this->Auth->getConfig('loginAction'));
        }

        // The password is right - whatever happens to the redirect target below, this account is
        // no longer under attack from this form, so the lockout counter starts from zero again.
        $usernameField = $this->Auth->getConfig('authenticate.all.fields.username');
        $this->clearFailedLoginCount($this->getRequest()->getData($usernameField));

        if ($session->check('Croogo.redirect')) {
            $redirectUrl = $session->read('Croogo.redirect');
            $session->delete('Croogo.redirect');
        } else {
            $redirectUrl = $this->Auth->redirectUrl();
        }

        if (!$this->urlAuthorized($user, $redirectUrl)) {
            // The credentials are right; only the redirect TARGET is off-limits for this role -
            // typically a deep link into a module the account was never granted (a new hire
            // opening the link a colleague sent). Refusing the whole login here, and counting
            // it as a failed attempt, is what surfaced as "Authorization error" on freshly
            // created accounts. Fall back to the dashboard when that is allowed; refuse only
            // when even the dashboard is denied, and never feed the failed-login counter -
            // the password was correct.
            $target = $this->urlToString($redirectUrl);
            $fallbackUrl = $this->authorizedFallbackUrl($user, $target);
            $this->log(sprintf(
                'Admin login of "%s" (role_id %s): redirect target %s is not authorized for the account, %s',
                $user['username'] ?? '?',
                $user['role_id'] ?? '?',
                $target,
                $fallbackUrl === null ? 'no authorized fallback - login refused' : 'falling back to ' . $fallbackUrl
            ), 'warning');

            if ($fallbackUrl === null) {
                $this->Auth->setConfig('authError', __d('croogo', 'Authorization error'));
                $this->Flash->error($this->Auth->getConfig('authError'), ['key' => 'auth']);

                return $this->redirect($this->Auth->getConfig('loginAction'));
            }
            $redirectUrl = $fallbackUrl;
        }

        $this->Auth->setUser($user);

        if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
            $user = $this->Users->get($user['id']);
            $user->password = $this->getRequest()->getData('password');
            $this->Users->save($user);
        }

        Croogo::dispatchEvent('Controller.Users.adminLoginSuccessful', $this);

        return $this->redirect($redirectUrl);
    }

    /**
     * First landing page the account may open when `$deniedTarget` is off-limits: `loginRedirect`
     * as configured by Croogo/Acl.Filter (`Site.dashboard_url`), then the bare `/admin` landing.
     * The two differ whenever `Site.dashboard_url` points at a plugin action a role was never
     * granted (Croogo's default is Dashboards) while `/admin` itself is open to everyone.
     *
     * @param array|\ArrayAccess $user Identified user.
     * @param string $deniedTarget Normalized target that was just refused.
     * @return string|null Normalized path, or null when nothing is authorized.
     */
    protected function authorizedFallbackUrl($user, string $deniedTarget): ?string
    {
        $candidates = [];
        foreach ([$this->Auth->getConfig('loginRedirect') ?: '/admin', '/admin'] as $url) {
            try {
                $candidates[] = $this->urlToString($url);
            } catch (\Throwable $e) {
                // An unroutable `Site.dashboard_url` must not cost the login - skip the candidate.
                $this->log('Admin login: loginRedirect is not routable: ' . $e->getMessage(), 'warning');
            }
        }
        foreach (array_unique($candidates) as $candidate) {
            if ($candidate === $deniedTarget) {
                continue;
            }
            if ($this->urlAuthorized($user, $candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * ACL verdict for a redirect target that never turns into a 500: `Access::isUrlAuthorized()`
     * parses the URL with the router, which throws for a non-routable one, and this runs on the
     * error path of every login. Anything that cannot be checked counts as not authorized.
     *
     * @param array|\ArrayAccess $user Identified user.
     * @param array|string $url Route array or path.
     * @return bool
     */
    protected function urlAuthorized($user, $url): bool
    {
        try {
            return (bool)$this->Access->isUrlAuthorized($user, $url);
        } catch (\Throwable $e) {
            $this->log(sprintf(
                'Admin login: could not authorize redirect target %s: %s',
                is_array($url) ? json_encode($url) : (string)$url,
                $e->getMessage()
            ), 'warning');

            return false;
        }
    }

    /**
     * @param array|string $url Route array or path.
     * @return string Normalized path, comparable with what Auth::redirectUrl() returns.
     */
    protected function urlToString($url): string
    {
        if (is_array($url)) {
            return Router::url($url + ['_base' => false]);
        }

        return Router::normalize((string)$url);
    }

    /**
     * Admin logout
     *
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function logout()
    {
        Croogo::dispatchEvent('Controller.Users.adminLogoutSuccessful', $this);
        $this->Flash->success(__d('croogo', 'Log out successful.'), ['key' => 'auth']);

        return $this->redirect($this->Auth->logout());
    }

    public function beforeLookup(Event $event): void
    {
        /** @var \Cake\ORM\Query\SelectQuery $query */
        $query = $event->getSubject()->query;

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
                'modified',
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

    public function beforePaginate(Event $event): void
    {
        /** @var \Cake\ORM\Query\SelectQuery $query */
        $query = $event->getSubject()->query;

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
            ->orderBy([
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
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function register()
    {
        if ($this->Auth->user('id')) {
            if (!$this->getRequest()->getSession()->check('Flash.auth') &&
                !$this->getRequest()->getSession()->check('Flash.flash')
            ) {
                $this->Flash->error(__d('croogo', 'You are already logged in'));
            }

            return $this->redirect($this->referer());
        }
        $user = $this->Users->newEntity();

        $this->set('user', $user);

        if (!$this->getRequest()->is('post')) {
            return;
        }

        $user = $this->Users->register($user, $this->getRequest()->getData());
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
        if (!$this->getRequest()->is('post')) {
            return;
        }

        $username = $this->getRequest()->getData('username');
        if (!$username) {
            $this->Flash->error(__d('croogo', 'Invalid username.'));

            return $this->redirect(['action' => 'forgot']);
        }

        $user = $this->Users
            ->find()
            ->where(['OR' => ['username' => $username, 'email' => $username]])
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
     * @return \Cake\Http\Response|void
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

        if (!$this->getRequest()->is('put')) {
            return;
        }

        // Change the password of the user entity
        $user = $this->Users->changePasswordFromReset($user, $this->getRequest()->getData());

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
