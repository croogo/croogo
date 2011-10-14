<?php
/**
 * Users Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UsersController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'Users';
/**
 * Components
 *
 * @var array
 * @access public
 */
    public $components = array(
        'Email',
    );
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('User');

    public function beforeFilter() {
        parent::beforeFilter();

        if ($this->request->is('post') && in_array($this->params['action'], array('admin_login', 'login'))) {
            $field = $this->Auth->authenticate['all']['fields']['username'];
            if (!empty($this->request->data) && empty($this->request->data['User'][$field])) {
                $this->redirect(array('action' => $this->params['action']));
            }
            $cacheName = 'auth_failed_' . $this->data['User'][$field];
            if (Cache::read($cacheName, 'users_login') >= Configure::read('User.failed_login_limit')) {
                $this->Session->setFlash(__('You have reached maximum limit for failed login attempts. Please try again after a few minutes.'), 'default', array('class' => 'error'));
                $this->redirect(array('action' => $this->params['action']));
            }
        }
    }

    public function beforeRender() {
        parent::beforeRender();

        if (in_array($this->params['action'], array('admin_login', 'login'))) {
            if ($this->request->is('post') && !empty($this->request->data)) {
                $field = $this->Auth->authenticate['all']['fields']['username'];
                $cacheName = 'auth_failed_' . $this->request->data['User'][$field];
                $cacheValue = Cache::read($cacheName, 'users_login');
                Cache::write($cacheName, (int)$cacheValue + 1, 'users_login');
            }
        }
    }

    public function admin_index() {
        $this->set('title_for_layout', __('Users'));

        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function admin_add() {
        if (!empty($this->data)) {
            $this->User->create();
            $this->data['User']['activation_key'] = md5(uniqid());
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('The User has been saved'), 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.'), 'default', array('class' => 'error'));
                unset($this->data['User']['password']);
            }
        } else {
            $this->data['User']['role_id'] = 2; // default Role: Registered
        }
        $roles = $this->User->Role->find('list');
        $this->set(compact('roles'));
    }

    public function admin_edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid User'), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('The User has been saved'), 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.'), 'default', array('class' => 'error'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->User->read(null, $id);
        }
        $roles = $this->User->Role->find('list');
        $this->set(compact('roles'));
    }

    public function admin_reset_password($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid User'), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            $user = $this->User->findById($id);
            if ($user['User']['password'] == Security::hash($this->data['User']['current_password'], null, true)) {
                if ($this->User->save($this->data)) {
                    $this->Session->setFlash(__('Password has been reset.'), 'default', array('class' => 'success'));
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Password could not be reset. Please, try again.'), 'default', array('class' => 'error'));
                }
            } else {
                $this->Session->setFlash(__('Current password did not match. Please, try again.'), 'default', array('class' => 'error'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->User->read(null, $id);
        }
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for User'), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->User->delete($id)) {
            $this->Session->setFlash(__('User deleted'), 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
        }
    }

    public function admin_login() {
        $this->set('title_for_layout', __('Admin Login'));
        $this->layout = "admin_login";
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash($this->Auth->authError, 'default', array(), 'auth');
                $this->redirect($this->Auth->loginAction);
            }
        }
    }

    public function admin_logout() {
        $this->Session->setFlash(__('Log out successful.'), 'default', array('class' => 'success'));
        $this->redirect($this->Auth->logout());
    }

    public function index() {
        $this->set('title_for_layout', __('Users'));
    }

    public function add() {
        $this->set('title_for_layout', __('Register'));
        if (!empty($this->data)) {
            $this->User->create();
            $this->data['User']['role_id'] = 2; // Registered
            $this->data['User']['activation_key'] = md5(uniqid());
            $this->data['User']['status'] = 0;
            $this->data['User']['username'] = htmlspecialchars($this->data['User']['username']);
            $this->data['User']['website'] = htmlspecialchars($this->data['User']['website']);
            $this->data['User']['name'] = htmlspecialchars($this->data['User']['name']);
            if ($this->User->save($this->data)) {
                $this->data['User']['password'] = null;
                $this->Email->from = Configure::read('Site.title') . ' '
                    . '<croogo@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])).'>';
                $this->Email->to = $this->data['User']['email'];
                $this->Email->subject = __('[' . Configure::read('Site.title') . '] Please activate your account');
                $this->Email->template = 'register';
                $this->set('user', $this->data);
                $this->Email->send();

                $this->Session->setFlash(__('You have successfully registered an account. An email has been sent with further instructions.'), 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'login'));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.'), 'default', array('class' => 'error'));
            }
        }
    }

    public function activate($username = null, $key = null) {
        if ($username == null || $key == null) {
            $this->redirect(array('action' => 'login'));
        }

        if ($this->User->hasAny(array(
                'User.username' => $username,
                'User.activation_key' => $key,
                'User.status' => 0,
            ))) {
            $user = $this->User->findByUsername($username);
            $this->User->id = $user['User']['id'];
            $this->User->saveField('status', 1);
            $this->User->saveField('activation_key', md5(uniqid()));
            $this->Session->setFlash(__('Account activated successfully.'), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('An error occurred.'), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'login'));
    }

    public function edit() {}

    public function forgot() {
        $this->set('title_for_layout', __('Forgot Password'));

        if (!empty($this->data) && isset($this->data['User']['username'])) {
            $user = $this->User->findByUsername($this->data['User']['username']);
            if (!isset($user['User']['id'])) {
                $this->Session->setFlash(__('Invalid username.'), 'default', array('class' => 'error'));
                $this->redirect(array('action' => 'login'));
            }

            $this->User->id = $user['User']['id'];
            $activationKey = md5(uniqid());
            $this->User->saveField('activation_key', $activationKey);
            $this->set(compact('user', 'activationKey'));

            $this->Email->from = Configure::read('Site.title') . ' '
                    . '<croogo@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])).'>';
            $this->Email->to = $user['User']['email'];
            $this->Email->subject = '[' . Configure::read('Site.title') . '] ' . __('Reset Password');
            $this->Email->template = 'forgot_password';
            if ($this->Email->send()) {
                $this->Session->setFlash(__('An email has been sent with instructions for resetting your password.'), 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'login'));
            } else {
                $this->Session->setFlash(__('An error occurred. Please try again.'), 'default', array('class' => 'error'));
            }
        }
    }

    public function reset($username = null, $key = null) {
        $this->set('title_for_layout', __('Reset Password'));

        if ($username == null || $key == null) {
            $this->Session->setFlash(__('An error occurred.'), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'login'));
        }

        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.username' => $username,
                'User.activation_key' => $key,
            ),
        ));
        if (!isset($user['User']['id'])) {
            $this->Session->setFlash(__('An error occurred.'), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'login'));
        }

        if (!empty($this->data) && isset($this->data['User']['password'])) {
            $this->User->id = $user['User']['id'];
            $user['User']['password'] = Security::hash($this->data['User']['password'], null, true);
            $user['User']['activation_key'] = md5(uniqid());
            if ($this->User->save($user['User'])) {
                $this->Session->setFlash(__('Your password has been reset successfully.'), 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'login'));
            } else {
                $this->Session->setFlash(__('An error occurred. Please try again.'), 'default', array('class' => 'error'));
            }
        }

        $this->set(compact('user', 'username', 'key'));
    }

    public function login() {
        $this->set('title_for_layout', __('Log in'));
    }

    public function logout() {
        $this->Session->setFlash(__('Log out successful.'), 'default', array('class' => 'success'));
        $this->redirect($this->Auth->logout());
    }

    public function view($username) {
        $user = $this->User->findByUsername($username);
        if (!isset($user['User']['id'])) {
            $this->Session->setFlash(__('Invalid User.'), 'default', array('class' => 'error'));
            $this->redirect('/');
        }

        $this->set('title_for_layout', $user['User']['name']);
        $this->set(compact('user'));
    }
    
}
?>