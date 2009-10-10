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
    var $name = 'Users';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('User');

    function beforeFilter() {
        parent::beforeFilter();
    }

    function admin_index() {
        $this->pageTitle = __('Users', true);

        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    function admin_add() {
        if (!empty($this->data)) {
            $this->User->create();
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('The User has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
            }
        }
        $roles = $this->User->Role->find('list');
        $this->set(compact('roles'));
    }

    function admin_edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid User', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('The User has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->User->read(null, $id);
        }
        $roles = $this->User->Role->find('list');
        $this->set(compact('roles'));
    }

    function admin_reset_password($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid User', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('Password has been reset.', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Password could not be reset. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->User->read(null, $id);
        }
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for User', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->User->del($id)) {
            $this->Session->setFlash(__('User deleted', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    function admin_login() {
        $this->pageTitle = __('Admin Login', true);
        $this->layout = "admin_login";
    }

    function admin_logout() {
        $this->Session->setFlash(__('Log out successful.', true));
        $this->redirect($this->Auth->logout());
        exit();
    }

    function index() {
        $this->pageTitle = __('Users', true);
    }

    function add() {
        $this->pageTitle = __('Register', true);
        if (!empty($this->data)) {
            $this->User->create();
            $this->data['User']['role_id'] = 2; // Registered
            $this->data['User']['activation_key'] = md5(uniqid());
            $this->data['User']['status'] = 0;
            if ($this->User->save($this->data)) {
                $this->data['User']['password'] = null;
                $this->Email->from = 'no-reply';
                $this->Email->to = $this->data['User']['email'];
                $this->Email->subject = __('[' . Configure::read('Site.title') . '] Please activate your account', true);
                $this->Email->template = 'register';
                $this->set('user', $this->data);
                $this->Email->send();

                $this->Session->setFlash(__('You have successfully registered an account. An email has been sent with further instructions.', true));
                $this->redirect(array('action' => 'login'));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
            }
        }
    }

    function activate($username = null, $key = null) {
        if ($username == null || $key == null) {
            $this->redirect(array('action' => 'login'));
            exit();
        }

        if ($this->User->hasAny(array(
                'User.username' => $username,
                'User.activation_key' => $key,
                'User.status' => 0,
            ))) {
            $user = $this->User->findByUsername($username);
            $this->User->id = $user['User']['id'];
            $this->User->saveField('status', 1);
            $this->Session->setFlash(__('Account activated successfully.', true));
        } else {
            $this->Session->setFlash(__('An error occurred.', true));
        }

        $this->redirect(array('action' => 'login'));
        exit();
    }

    function edit() {}
    function forgot() {}
    function reset() {}

    function login() {
        $this->pageTitle = __('Log in', true);
    }

    function logout() {
        $this->Session->setFlash(__('Log out successful.', true));
        $this->redirect($this->Auth->logout());
        exit();
    }

    function view($username) {
        $user = $this->User->findByUsername($username);
        if (!isset($user['User']['id'])) {
            $this->Session->setFlash(__('Invalid User.', true));
            $this->redirect('/');
        }

        $this->pageTitle = $user['User']['name'];
        $this->set(compact('user'));
    }
    
}
?>