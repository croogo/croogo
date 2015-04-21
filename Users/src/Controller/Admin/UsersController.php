<?php

namespace Croogo\Users\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Croogo\Croogo\Controller\CroogoAppController;
use Croogo\Croogo\Croogo;

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
class UsersController extends CroogoAppController {

/**
 * Initialize
 *
 * @return void
 */
	public function initialize() {
		parent::initialize();

		$this->loadComponent('Search.Prg', [
			'presetForm' => [
				'paramType' => 'querystring',
			],
			'commonProcess' => [
				'paramType' => 'querystring',
				'filterEmpty' => true,
			],
		]);
	}

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return parent::implementedEvents() + [
			'Controller.Users.beforeAdminLogin' => 'onBeforeAdminLogin',
			'Controller.Users.adminLoginFailure' => 'onAdminLoginFailure',
		];
	}

/**
 * Notify user when failed_login_limit hash been hit
 *
 * @return bool
 */
	public function onBeforeAdminLogin() {
		$field = $this->Auth->authenticate['all']['fields']['username'];
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
	public function onAdminLoginFailure() {
		$field = $this->Auth->authenticate['all']['fields']['username'];
		if (empty($this->request->data)) {
			return true;
		}
		$cacheName = 'auth_failed_' . $this->request->data[$field];
		$cacheValue = Cache::read($cacheName, 'users_login');
		Cache::write($cacheName, (int)$cacheValue + 1, 'users_login');
		return true;
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 * $searchField : Identify fields for search
 */
	public function index() {
		$this->Prg->commonProcess();
		$searchFields = ['role_id', 'name'];

		$criteria = $this->Users->find('searchable', $this->Prg->parsedParams());

		$this->set('users', $this->paginate($criteria));
		$this->set('roles', $this->Users->Roles->find('list'));
		$this->set('displayFields', $this->Users->displayFields());
		$this->set('searchFields', $searchFields);

		if (isset($this->request->query['chooser'])) {
			$this->layout = 'admin_popup';
		}
	}


/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function add() {
		if (!empty($this->request->data)) {
			$this->User->create();
			$this->request->data['User']['activation_key'] = md5(uniqid());
			if ($this->User->save($this->request->data)) {
				$this->request->data['User']['id'] = $this->User->id;
				$this->__sendActivationEmail();

				$this->Flash->success(__d('croogo', 'The User has been saved'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__d('croogo', 'The User could not be saved. Please, try again.'));
				unset($this->request->data['User']['password']);
			}
		} else {
			$this->request->data['User']['role_id'] = 2; // default Role: Registered
		}
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles'));
	}

/**
 * Send activation email
 */
	private function __sendActivationEmail() {
		if (empty($this->request->data['User']['notification'])) {
			return;
		}

		$user = $this->request->data['User'];
		$activationUrl = Router::url([
			'prefix' => 'admin',
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'activate',
			$user['username'],
			$user['activation_key'],
		], true);
		$this->_sendEmail(
			[Configure::read('Site.title'), $this->_getSenderEmail()],
			$user['email'],
			__d('croogo', '[%s] Please activate your account', Configure::read('Site.title')),
			'Users.register',
			'user activation',
			$this->theme,
			[
				'user' => $this->request->data,
				'url' => $activationUrl,
			]
		);
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function edit($id = null) {
		if (!empty($this->request->data)) {
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__d('croogo', 'The User has been saved'));
				return $this->Croogo->redirect(['action' => 'edit', $this->User->id]);
			} else {
				$this->Flash->error(__d('croogo', 'The User could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles'));
		$this->set('editFields', $this->User->editFields());
	}

/**
 * Admin reset password
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function reset_password($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Flash->error(__d('croogo', 'Invalid User'));
			return $this->redirect(['action' => 'index']);
		}
		if (!empty($this->request->data)) {
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__d('croogo', 'Password has been reset.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__d('croogo', 'Password could not be reset. Please, try again.'));
			}
		}
		$this->request->data = $this->User->findById($id);
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function delete($id = null) {
		if (!$id) {
			$this->Flash->error(__d('croogo', 'Invalid id for User'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->User->delete($id)) {
			$this->Flash->success(__d('croogo', 'User deleted'));
			return $this->redirect(['action' => 'index']);
		} else {
			$this->Flash->error(__d('croogo', 'User cannot be deleted'));
			return $this->redirect(['action' => 'index']);
		}
	}

/**
 * Admin login
 *
 * @return void
 * @access public
 */
	public function login() {
		$this->layout = 'admin_login';
		if ($this->Auth->user('id')) {
			if (!$this->request->session()->check('Message.auth')) {
				$this->Flash->alert(__d('croogo', 'You are already logged in'), ['key' => 'auth']);
			}
			return $this->redirect($this->Auth->redirectUrl());
		}
		if ($this->request->is('post')) {
			Croogo::dispatchEvent('Controller.Users.beforeAdminLogin', $this);
			$user = $this->Auth->identify();
			if ($user) {
				$this->Auth->setUser($user);

				Croogo::dispatchEvent('Controller.Users.adminLoginSuccessful', $this);
				return $this->redirect($this->Auth->redirectUrl());
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
	public function logout() {
		Croogo::dispatchEvent('Controller.Users.adminLogoutSuccessful', $this);
		$this->Flash->success(__d('croogo', 'Log out successful.'));
		return $this->redirect($this->Auth->logout());
	}
}
