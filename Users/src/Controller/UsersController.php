<?php

namespace Croogo\Users\Controller;

use Croogo\Core\Croogo;
use Croogo\Users\Model\Table\UsersTable;

/**
 * Users Controller
 *
 * @property UsersTable Users
 * @category Controller
 * @package  Croogo.Users.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UsersController extends AppController
{

    /**
     * {inheritdoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['logout']);
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Users'));
    }

    /**
     * Add
     *
     * @return void
     * @access public
     */
    public function add()
    {
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
     * Activate
     *
     * @param string $username
     * @param string $activationKey
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function activate($username, $activationKey)
    {
        // Get the user with the activation key from the database
        $user = $this->Users
            ->find()
            ->where([
                'username' => $username,
                'activation_key' => $activationKey
            ])
            ->first();
        if (!$user) {
            $this->Flash->error(__d('croogo', 'Could not activate your account.'));

            return $this->redirect(['action' => 'login']);
        }

        // Activate the user
        $user = $this->Users->activate($user);
        if (!$user) {
            $this->Flash->error(__d('croogo', 'Could not activate your account'));

            return $this->redirect(['action' => 'login']);
        }

        $this->Flash->success(__d('croogo', 'Account activated successfully.'));

        return $this->redirect(['action' => 'login']);
    }

    /**
     * Edit
     *
     * @return void
     * @access public
     */
    public function edit()
    {
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

        $user = $this->Users
            ->findByUsername($this->getRequest()->data('username'))
            ->first();
        if (!$user) {
            $this->Flash->error(__d('croogo', 'Invalid username.'));

            return $this->redirect(['action' => 'forgot']);
        }

        if (empty($user->email)) {
            $this->Flash->error(__d('croogo', 'Invalid email.'));

            return;
        }

        $options = [
            'prefix' => $this->getRequest()->getParam('prefix'),
        ];
        $success = $this->Users->resetPassword($user, $options);
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

    /**
     * Login
     *
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function login()
    {
        $session = $this->getRequest()->session();
        if (!$this->getRequest()->is('post')) {
            $redirectUrl = $this->Auth->redirectUrl();
            if ($redirectUrl != '/' && !$session->check('Croogo.redirect')) {
                $session->write('Croogo.redirect', $redirectUrl);
            }

            return;
        }

        Croogo::dispatchEvent('Controller.Users.beforeLogin', $this);

        $user = $this->Auth->identify();
        if (!$user) {
            Croogo::dispatchEvent('Controller.Users.loginFailure', $this);

            $this->Flash->error($this->Auth->config('authError'));

            return $this->redirect($this->Auth->loginAction);
        }

        if ($session->check('Croogo.redirect')) {
            $redirectUrl = $session->read('Croogo.redirect');
            $session->delete('Croogo.redirect');
        } else {
            $redirectUrl = $this->Auth->redirectUrl();
        }

        if (!$this->Access->isUrlAuthorized($user, $redirectUrl)) {
            Croogo::dispatchEvent('Controller.Users.loginFailure', $this);
            $this->Auth->config('authError', __d('croogo', 'Authorization error'));
            $this->Flash->error($this->Auth->config('authError'));

            return $this->redirect($this->Auth->loginRedirect);
        }

        $this->Auth->setUser($user);

        Croogo::dispatchEvent('Controller.Users.loginSuccessful', $this);

        return $this->redirect($redirectUrl);
    }

    /**
     * Logout
     *
     * @access public
     */
    public function logout()
    {
        Croogo::dispatchEvent('Controller.Users.beforeLogout', $this);
        $this->getRequest()->session()->delete('Croogo.redirect');

        $this->Flash->success(__d('croogo', 'Log out successful.'), 'auth');

        $logoutUrl = $this->Auth->logout();
        Croogo::dispatchEvent('Controller.Users.afterLogout', $this);

        return $this->redirect($logoutUrl);
    }

    /**
     * View
     *
     * @param string $username
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function view($username = null)
    {
        if ($username == null) {
            $username = $this->Auth->user('username');
        }
        $user = $this->Users->findByUsername($username)->first();
        if (!$user) {
            $this->Flash->error(__d('croogo', 'Invalid User.'));

            return $this->redirect('/');
        }
        $this->set('title_for_layout', $user->name);
        $this->set(compact('user'));
    }

    protected function _getSenderEmail()
    {
        return 'croogo@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
    }
}
